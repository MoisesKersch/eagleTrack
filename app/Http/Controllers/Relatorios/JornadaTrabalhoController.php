<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Motorista;
use App\Models\Bilhete;
use App\Models\Cliente;
use App\Models\FechamentoFolha;
use App\Models\Veiculo;
use App\Models\IgnicaoVeiculos;
use DB;
use Excel;
use App\Helpers\DataHelper;
use App\Helpers\PdfHelper;
use App\Services\JornadaTrabalhoLivreService;
use App\Helpers\ExportaHelper;
use App\Services\JornadaTrabalhoService;

class JornadaTrabalhoController extends Controller
{
    public function listar()
    {
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        return view('relatorios.jornadaTrabalho.listar', compact('clientes'));
    }
    public function relatorio(Request $request)
    {
        $jornada = $this->query($request);
        foreach ($jornada as $i => $jorm) {
            $totalHoras = $jorm['totalhoras'];
            $trabalhadas = new \Datetime($jorm['dfehoratrabalhada']);
            $trabalhadas = $trabalhadas->format('H:i:s');
            $jornada[$i] += ['trabalhadas' => $trabalhadas];
            $jornada[$i] += ['totalhoras' => $totalHoras];
        }
        return response([
            'jornada' => $jornada,
            'usuario' => \Auth::user()->name,
        ]);
    }
    public function todos(Request $request)
    {
        $idCli = Veiculo::select('vecodigo')
            ->join('clientes', 'clcodigo', '=', 'veproprietario')
            ->join('usuarios_clientes', 'clcodigo', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)->get();

        $id = '';
        foreach($idCli as $i){
            $id .= $i->vecodigo.',';
        }
        $request->buscar = trim($id, ',');

        $dados = $this->query($request);

        $clientes = Cliente::with('motoristas')
            ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)
            ->get();

        return response ([
            'dados' => $dados,
            'clientes' => $clientes,
        ]);
    }

    public function exportar(Request $request)
    {
        if($request->tipo == 'pdf'){
            $pdf = new ExportaHelper;
            $arquivo = $pdf->converteHtmlPdf($request->titulo, $request->html);
            return response(['dados'=>$arquivo]);
        }
        else{
            $arquivo = new ExportaHelper;
            //converte array para formato funcao exportacao excel/csv
            $arrayDados = json_decode($request->arrayDados);
            $arrayExcel;
            $arrayExcel[] = array("DATA","NOME","SEMANA","TRABALHADAS","FALTA","EXTRA","EXTRA 100%","AD. NOTURNO","EXTRA NOTURNO","HORA ESPERA","INT. REFEICAO","TOTAL");
            foreach($arrayDados as $data=>$linha){
                $arrayExcel[] = array($linha[0],
                      $linha[1],
                      $linha[2],
                      $linha[3],
                      $linha[4],
                      $linha[5],
                      $linha[6],
                      $linha[7],
                      $linha[8],
                      $linha[9],
                      $linha[10],
                      $linha[11]
                    );
             }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('Jornada de Trabalho',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('Jornada de Trabalho',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }

    public function query($request)
    {
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        if(gettype($request->buscar) == 'array'){
            $id = implode(", ", $request->buscar);
        }else {
            $id = $request->buscar;
        }

        if(empty($id)) {
            return $trabalho = '';
        }
        $dataInicio = $request->data_inicio.' 00:00:00';
        $dataFim = $request->data_fim. ' 23:59:49';

        $trabalho = DB::table('fechamento_folhas')
            ->select('fedataentrada as afedataentrada', 'mtnome as bmtnome',
            'fehoratrabalhada as dfehoratrabalhada', 'feextranoturno as ifeextranoturno',
            'fedsr', 'fehorafalta as efehorafalta', 'fehoraextra as ffehoraextra',
            'fehoracemcento as gfehoracemcento', 'fehoranoturna as hfehoranoturna',
            'fehoraespera as jfehoraespera', 'feintervalo as kfeintervalo', 'fehorastotal as totalhoras')
            ->where('fedataentrada', '>', $dataInicio)
            ->join('motoristas', 'mtcodigo', '=', 'femotorista')
            ->where('fedataentrada', '<', $dataFim)
            ->whereRaw('femotorista in ('.$id.')')
            ->orderBy('bmtnome','ASC')
            ->orderBy('afedataentrada','ASC')
            ->get();

        foreach($trabalho as $i => $trab) {
            $entrada = new \DateTime($trab->afedataentrada);
            $trabalho[$i]->afedataentrada = $entrada->format('d/m/Y H:i:s');
            $trabalho[$i]->csemana = strftime('%A', strtotime($entrada->format('Y-m-d')));
            $trabalho[$i]->gfehoracemcento = $trab->gfehoracemcento ? : '00:00:00';
            $trabalho[$i]->hfehoranoturna = $trab->hfehoranoturna ? : '00:00:00';
            $array = (array)$trabalho[$i];
            ksort($array);
            $trabalho[$i] = $array;
        }

        // $trabalho = $this->agruparPorMotorista($trabalho)

        return $trabalho;
    }

    /*SCRITP GERACAO JORNADA DE TRABALHO*/
    public function script(Request $request)
    {
        $jornadaService = new JornadaTrabalhoService;
        $status = $jornadaService->calculaHoras($request->d);

    }//fim funcao script


    public function cliente(Request $request)
    {
        $id = $request->id;
        if(empty($id)) {
            return response ([
                'motoristas' => '',
            ]);
        }
        $motoristas = Cliente::select('mtcodigo', 'mtnome')
            ->join('motoristas', 'mtcliente', '=', 'clcodigo')
            ->whereIn('clcodigo', $id)->get();

        return response ([
            'motoristas' => $motoristas,
        ]);
    }

    public function jornadaLivre(Request $request)
    {
        $service = new JornadaTrabalhoLivreService;
        $service->script($request);
    }
}

<?php

namespace App\Http\Controllers\Relatorios;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use DB;
use App\Helpers\MapaHelper;
use App\Helpers\DataHelper;
use App\Helpers\ExportaHelper;
use App\Services\TempoParadoService;

class TempoParadoController extends Controller
{
    public function listar()
    {
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        return view('relatorios.tempoParado.listar', compact('clientes'));
    }
    public function relatorio(Request $request)
    {
        $tempoParadoService = new TempoParadoService;
        $tempoParado = $tempoParadoService->query($request);
        return response ([
            'tempoParado' => $tempoParado
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
            $arrayExcel[] = array("Placa","Data","Hora Inicio","Hora Fim","Tempo","Endereco","Ponto","Região","ignicao");
            foreach($arrayDados as $placa=>$placas){
                     foreach($placas as $data=>$datas){
                        foreach($datas as $key=>$linha){
                            if($key == 'totalizadorTempo'){
                                continue;
                            }else{
                                $arrayExcel[] = array($linha->biplaca.' | '.$linha->mtnome,
                                                      $linha->data,
                                                      $linha->dataInicio,
                                                      $linha->dataFim,
                                                      $linha->tempo,
                                                      $linha->biendereco,
                                                      $linha->ponto,
                                                      $linha->regiao,
                                                      $linha->biignicao
                                                    );
                            }
                        }
                    }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('TempoParado',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('TempoParado',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }

    public function todos(Request $request)
    {

        $clientes = Cliente::with('veiculos')
            ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)
            ->get();

        return response([
            'clientes' => $clientes
        ]);
    }
}

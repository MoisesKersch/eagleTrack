<?php

namespace App\Http\Controllers\Relatorios;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use DB;
use App\Helpers\MapaHelper;
use App\Helpers\DataHelper;
use App\Helpers\ExportaHelper;
use App\Services\AcionamentoPortasService;

class AcionamentoPortasController extends Controller
{
    public function listar()
    {
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        return view('relatorios.acionamentoPortas.listar', compact('clientes'));
    }

    public function relatorio(Request $request)
    {
        $apService = new AcionamentoPortasService;
        $acionamentoPortas = $apService->query($request);

        return response ([
            'acionamentoPortas' => $acionamentoPortas
        ]);
    }

    public function exportar(Request $request)
    {
        if ($request->tipo == 'pdf') {
            $pdf = new ExportaHelper;
            $arquivo = $pdf->converteHtmlPdf($request->titulo, $request->html);
            return response(['dados'=>$arquivo]);
        } else {
            $arquivo = new ExportaHelper;
            //converte array para formato funcao exportacao excel/csv
            $arrayDados = json_decode($request->arrayDados);
            $arrayExcel;
            $arrayExcel[] = array("Placa","Porta","Data Inicio","Data Fim","Tempo","Endereço","Local");
            foreach($arrayDados as $placa=>$placas){
                if (!is_object($placas)) {
                    // $arrayExcel[] = array($placas, '');
                    continue;
                }
                foreach($placas as $data=>$datas){
                    if (!is_object($datas)) {
                        // $arrayExcel[] = array($data, $datas);
                        continue;
                    }
                    foreach($datas as $key=>$linha){
                        if($key == 'totalizadorTempo' || !is_object($linha)){
                            continue;
                        }else{
                            $arrayExcel[] = array(
                                                $placa,
                                                $linha->numPorta,
                                                $linha->data,
                                                // $linha->dataInicio,
                                                $linha->dataFinal,
                                                $linha->tmpPortaAberta,
                                                $linha->endereco,
                                                $linha->localposicao,
                                                // $linha->biignicao
                                            );
                        }
                    }
                }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('AcionamentoPorta',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('AcionamentoPorta',$arrayExcel);
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

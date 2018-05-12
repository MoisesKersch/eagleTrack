<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ExportaHelper;
use App\Services\TempoIgnicaoService;
use App\Models\Cliente;
use App\Models\Veiculo;
use App\Models\User;

class TempoIgnicaoController extends Controller
{
    public function listar()
    {
        $veiculos = new Veiculo;
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        }else{
            $clientes = \Auth::user()->clientes;
            $arrClientes = \Auth::user()->getEmpresasUsuario();
            $veiculos = $veiculos->getVeiculos($arrClientes);
        }
        return view('relatorios.tempoIgnicao.listar', compact('clientes','veiculos'));
    }

    public function gerar(Request $request)
    {
        $query = new TempoIgnicaoService;
        $query = $query->query($request);
        return response([
            'dados' => $query
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
            $arrayExcel[] = array("Placa","Hora Inicio","Hora Final","Tempo","Motorista");
            foreach($arrayDados as $placa=>$placas){
                     foreach($placas as $data=>$datas){
                        foreach($datas as $key=>$linha){
                            if($key == 'tempoTotalDia'){
                                continue;
                            }else{
                                $arrayExcel[] = array($linha->placa,
                                                      $linha->dataInicio,
                                                      $linha->dataFim,
                                                      $linha->tempo,
                                                      $linha->motorista
                                                    );
                            }
                        }
                    }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('TempoIginicaoLigada',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('TempoIginicaoLigada',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }
}

<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\GrupoMotorista;
use App\Helpers\ExportaHelper;
use Auth;
use DB;
//use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Bilhete;
use App\Models\Regioes;
use App\Models\Modulo;
use App\Models\ComandosFila;
use App\Helpers\DataHelper;
use App\Services\ComunicacaoService;
use App\Services\VeiculoService;
use DateTime;

class ComunicacaoController extends Controller
{
    public function listar()
    {
        
        $hora = date('H:i:S');
        $veiculos = new Veiculo;
        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
            $veiculos = []; 
            $grupoMotorista = [];   
        } else {
            $clientes = \Auth::user()->clientes;
            $arrayClientes = array();
            foreach($clientes as $cliente){
                $arrayClientes[] = $cliente->clcodigo;
            }
            $veiculos = $veiculos->getVeiculos($arrayClientes);
            $grupoMotorista = new GrupoMotorista;
            $grupoMotorista = $grupoMotorista->getGrpMotorista($arrayClientes);
        }

        return view('relatorios.comunicacao.listar', compact('clientes','veiculos'));
    }
    //***********************************************************************************


public function gerar(Request $request){
    $relatorio = new ComunicacaoService();
    $relatorio = $relatorio->query($request->tempo,$request->veiculos);
    return response([$relatorio]);
}

public function dadosFiltros(Request $request){
    $veiculos = Veiculo::select('veplaca', 'vecodigo')
        ->where('vestatus','A')
        ->whereIn('veproprietario', $request->clientes)->get();

    return response ([
        'veiculos'=>$veiculos
    ]);
}

//****************************************************************************************

public function carregaVeiculos(Request $request){
    $veiculo = new Veiculo();
    $veiculos = $veiculo->getVeiculos($request->empresas);
    return response([$veiculos]);
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
        $arrayExcel[] = array("Placa","fantasia", "Ultima/posição","Local","Modulo","Modelo");
        foreach($arrayDados as $placa=>$placas){
            foreach($placas as $data){
                // foreach($datas as $key=>$linha){
                    if($data == 'modulo'){
                        continue;
                    }else{
                        $arrayExcel[] = array($data->veplaca,
                                              $data->clfantasia,
                                              $data->moultimoevento,
                                              $data->moultimoendereco,
                                              $data->mocodigo,
                                              $data->mmdescricao,
                                            );
                        }
                    // }
                }
            }
        if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('comunicacao',$arrayExcel);
        if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('comunicacao',$arrayExcel);
        return response(['dados'=>$retorno]);
        }
    }

}

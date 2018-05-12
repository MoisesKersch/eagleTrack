<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Motorista;
use App\Models\GrupoMotorista;
use App\Models\Bilhete;
use App\Services\HistoricoPosicaoService;
use App\Helpers\ExportaHelper;

class HistoricoPosicoesController extends Controller
{
    public function listar()
    {
        $veiculo = new Veiculo();
        $cliente = new Cliente();
        $grpMotorista = new GrupoMotorista();
        $veiculos;
        $grpMotoristas = null;
        $empresas;
        $empresasUsuario = Auth::user()->getEmpresasUsuario();
        if(Auth::user()->usumaster == 'S'){
            $empresas = $cliente->getClientesAtivos();
            return view('relatorios.historicoPosicoes.listar', compact('empresas','grpMotoristas'));
        }else{
            $veiculos = $veiculo->getVeiculos($empresasUsuario);
            $empresas = $cliente->whereIn('clcodigo',$empresasUsuario)->get();
            $grpMotoristas = $grpMotorista->where('gmstatus','=','A')
                                          ->whereIn('gmcliente',$empresasUsuario)
                                          ->get();

            return view('relatorios.historicoPosicoes.listar', compact('veiculos','empresas','motoristas','grpMotoristas'));
        }
    }
    public function carregaVeiculos(Request $request){
        $veiculo = new Veiculo();
        $veiculos = $veiculo->getVeiculos($request->empresas);
        return response([$veiculos]);
    }
    public function carregaGrpMotoristas(Request $request){
        $grpMotorista = new GrupoMotorista();
        $grpMotoristas = $grpMotorista->getGrpMotorista($request->empresas);
        return response([$grpMotoristas]);
    }
    public function gerar(Request $request){
        $relatorio = new HistoricoPosicaoService();
        $relatorio = $relatorio->gerarHistoricoPosicoes($request->dini,$request->dfim,$request->empresas,$request->veiculos,$request->grupos);
        return response([$relatorio]);
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
            $arrayExcel[] = array("Placa","Data/Hora","Tempo","Evento","Endereco","Cidade","Local");
            foreach($arrayDados as $placa=>$placas){
                     foreach($placas as $data=>$datas){
                        foreach($datas as $key=>$linha){
                            if($key == 'eventos'){
                                continue;
                            }else{
                                $arrayExcel[] = array($linha->biplaca,
                                                      $linha->bidataevento,
                                                      $linha->tempoExtenso,
                                                      $linha->tipoEvento,
                                                      $linha->rua,
                                                      $linha->cidade,
                                                      $linha->pontoReferencia
                                                    );
                            }
                        }
                    }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('HistoricoPosicoes',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('HistoricoPosicoes',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }
}

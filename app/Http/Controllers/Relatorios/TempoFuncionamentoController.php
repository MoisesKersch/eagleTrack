<?php

namespace App\Http\Controllers\Relatorios;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Services\TempoFuncionamentoService;
use App\Helpers\ExportaHelper;

class TempoFuncionamentoController extends Controller
{
    public function listar()
    {
        $veiculos = new Veiculo;
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')
                                        ->where('clstatus', '=', 'A')
                                        ->get();
        }else{
            $clientes = \Auth::user()->clientes;
            $arrClientes = \Auth::user()->getEmpresasUsuario();
            $veiculos = $veiculos->getVeiculos($arrClientes);
        }
        return view('relatorios.tempoFuncionamento.listar', compact('clientes','veiculos'));
    }
    
    public function relatorio(Request $request)
    {
        $tempoFuncionamento = new TempoFuncionamentoService;
        $tempoFuncionamento = $tempoFuncionamento->consulta($request);
        return response(['dados' => $tempoFuncionamento]);
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
            $arrayExcel[] = array("Prefixo","Data","Inicio","Fim","Tempo","Ponto","Referencia");
            foreach($arrayDados as $p => $placas){
                     foreach($placas as $d => $datas){
                        foreach($datas as $l => $linha){
                            if($l == 'tempoTotal'){
                                continue;
                            }else{
                                $arrayExcel[] = array($p,
                                                      $d,
                                                      $linha->inicio,
                                                      $linha->fim,
                                                      $linha->tempo,
                                                      $linha->ponto,
                                                      $linha->referencia
                                                    );
                            }
                        }
                    }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('TempoFuncionamento',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('TempoFuncionamento',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }

}

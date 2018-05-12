<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Helpers\PdfHelper;
use App\Models\Cliente;
use App\Models\Regioes;
use App\Services\RegiaoService;
use App\Helpers\ExportaHelper;
use Excel;

class RegiaoController extends Controller
{
    public function listar()
    {
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        return view('relatorios.regiao.listar', compact('clientes'));
    }

    public function dadosFiltros(Request $request){

        $veiculos = Veiculo::select('veplaca', 'vecodigo','veprefixo')
            ->whereIn('veproprietario', $request->clientes)
            ->where('vestatus','A')->get();

        $regioes = Regioes::select('recodigo','redescricao')
            ->whereIn('recliente', $request->clientes)->get();

        return response ([
            'veiculos'=>$veiculos,
            'regioes'=>$regioes
        ]);
    }

    public function relatorio(Request $request)
    {
        $placas = '';
        if( empty($request->clientes_regioes) && empty($request->regiao_regioes) && empty($request->regiao_veics)) {
            return response([
                'dados' => ''
            ]);
        }

        $regService = new RegiaoService;

        $dados = $regService->query($request);
        return response([
            'dados' => $dados
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
            $arrayExcel[] = array("DATA HORA ENTRADA","DATA HORA SAIDA","ÁREA","KMS","VEL. MÉDIA","PARADAS");
            foreach($arrayDados as $placa=>$placas){
                     foreach($placas as $data=>$linha){
                        $arrayExcel[] = array($linha->hora_entrada,
                                              $linha->hora_saida,
                                              $linha->redescricao,
                                              $linha->kms,
                                              $linha->velocidade_media,
                                              $linha->qtd_paradas
                                            );
                    }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('Regiões',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('Regiões',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }
}

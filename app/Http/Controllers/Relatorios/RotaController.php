<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Helpers\PdfHelper;
use App\Models\Cliente;
use App\Models\Regioes;
use App\Models\Rota;
use App\Services\RotaService;
use App\Helpers\ExportaHelper;
use DB;
use Excel;

class RotaController extends Controller
{

    // public function relatorio(Request $request)
    public function relatorio(Request $request)
    {
        $placas = '';
        $rotaService = new RotaService;
        $dados = $rotaService->query($request);
        $file = $this->exportar($request, $dados);

        return response([
            'file' => $file
        ]);
    }

    public function exportar(Request $request, $dados)
    {

        if($request->tipo == 'pdf'){
            $pdf = new ExportaHelper;

            $html = "";
            $titulo = "Rota";
            $count = 0;

            foreach ($dados as $i => $rota) {
                if($count > 0){
                    $html .= "<div class=\"container\">
                                    <h1>$titulo</h1>
                                    <div class=\"float-left folha-cabecalho\">
                                        <span>Relatório Simplificado: Relatório de <span style='text-transform:lowercase;'>$titulo</span></span>
                                        <span>Emitido por: ".\Auth::user()->name."| Emissão: ".date('d/m/Y')."  </span>
                                    </div>
                                    <div classs='col-sm-12'>
                                        <span class='col-sm-12'>Ponto saída: ".$rota['ropontosaida']."</span>
                                        <span class='col-sm-12'>Ponto retorno: ".$rota['ropontoretorno']."</span>
                                    </div>
                                    ";
                }else{
                    $html .= "<div classs='col-sm-12'>
                        <span class='col-sm-12'>Ponto saída: ".$rota['ropontosaida']."</span>
                        <span class='col-sm-12'>Ponto retorno: ".$rota['ropontoretorno']."</span>
                    </div>";
                }

                $html .= "<table class=\"hover table\">
                            <thead>
                                <tr>
                                  <th>CODIGO</th>
                                  <th>ORDEM</th>
                                  <th>LOCAL ENTREGA</th>
                                  <th>CUBAGEM</th>
                                  <th>PESO</th>
                                  <th>QUANTIDADE</th>
                                </tr>
                            </thead>";

                $placa = $rota[0]["irplaca"];

                $html .= "<div><tr>
                                  <td class=\"badge\" colspan=\"5\"> $placa </td>
                                </tr>";
                $cont = 0;
                foreach ($rota as $j => $itemRota) {
                    if($cont > 2){
                        $html .= "<tr>
                                    <td>".$itemRota["irrota"]."</td>
                                    <td>".$itemRota["irordem"]."</td>
                                    <td>".$itemRota["podescricao"]."</td>
                                    <td>".$itemRota["ircubagem"]."</td>
                                    <td>".$itemRota["irpeso"]."</td>
                                    <td>".$itemRota["irqtde"]."</td>
                                </tr>";
                    }
                    $cont = $cont+1;
                }
                $html .= "</table>";
                if( $count > 0 ){
                    $html .= "</div>";
                }
                $html .= "<div style=\"page-break-before:always\" > </div>";
                $count ++;
            }

            $arquivo = $pdf->converteHtmlPdf($titulo, $html);
            return response(['dados'=>$arquivo]);
        }
        else{
            $arquivo = new ExportaHelper;
            //converte array para formato funcao exportacao excel/csv
            $retorno = "";
            $arrayDados = $dados;
            $arrayExcel;
            $arrayExcel[] = array("ROTA","DESCRICAO PONTO","PLACA","DATA","CUBAGEM","PESO","QUANTIDADE","ORDEM");
            // dd($arrayDados);
            foreach($arrayDados as $placa=>$placas){
                     foreach($placas as $data=>$linha){
                        $arrayExcel[] = array($linha["irrota"],
                                              $linha["podescricao"],
                                              $linha["irplaca"],
                                              $linha["irdata"],
                                              $linha["ircubagem"],
                                              $linha["irpeso"],
                                              $linha["irqtde"],
                                              $linha["irordem"],
                                            );
                    }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('Rota',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('Rota',$arrayExcel);

            return response(['dados'=>$retorno]);
        }
    }

    public function getAllRotas(Request $request){
         // $id_user = \Auth::user()->getEmpresasUsuario();
        $id_user = $request->empresas;

         if(sizeof($id_user) > 0){
                $lista = array();
             foreach ($request->empresas as $key => $value) {
                array_push($lista, intval($value));
             }

            $placa = $request->placa;
            $rotas = Rota::select()->with('itensRota')->with('itensRota.ponto')
            ->with('pontoSaida')->with('pontoRetorno')
            ->join('veiculos','rotas.roplaca','veiculos.veplaca')
            ->join('modulos','veiculos.vemodulo','modulos.mocodigo')
            ->whereIn('rocliente',$id_user)
            ->where(function($query) use($request){
                $query->where('rostatus','F')
              ->orWhere('rodata',$request->date);
            })
           // ->where('vestatus','A')
            ->addSelect(DB::raw("(select mtnome from motoristas where mtcodigo = veiculos.vemotorista) as mtmotorista"))
            ->addSelect(DB::raw("(select mtnome from motoristas where mtcodigo = veiculos.veajudante) as mtajudante"))
            ->addSelect(DB::raw("(select rohodometroinicio from rotas limit 1) as hodometroinicio"));

            // if(\Auth::user()->usumaster != 'S') {
            //     $rotas = $rotas->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : []);
            // }
            $rotas = $rotas->get();
            return response(['response'=>$rotas]);
        }
        return ['status'=>500];
    }
}

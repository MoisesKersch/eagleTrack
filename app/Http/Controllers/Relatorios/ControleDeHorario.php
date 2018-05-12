<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Motorista;
use App\Models\Veiculo;
use App\Models\Bilhete;
use App\Helpers\PontosHelper;
use App\Helpers\ExportaHelper;
use Excel;
use App\Helpers\PdfHelper;
use App\Services\ControleHorariosService;

class ControleDeHorario extends Controller
{
    public function listar()
    {
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        return view('relatorios.controleDeHorario.listar', compact('clientes'));
    }
    public function placaMorotista(Request $request)
    {
        $motoristas = Motorista::select('mtcodigo', 'mtnome');
            if(!(in_array('T', $request->id))) {
                $motoristas->where('mtstatus','A');
                $motoristas->whereIn('mtcliente', $request->id);
            }


        $veiculos = Veiculo::select('vecodigo', 'veplaca','veprefixo');
            if(!(in_array('T', $request->id))) {
                $veiculos->where('vestatus','A');
                $veiculos->whereIn('veproprietario', $request->id);
            }
        $motoristas = $motoristas->get();
        $veiculos = $veiculos->get();

        return response ([
            'motoristas' => $motoristas,
            'veiculos' => $veiculos,
        ]);
    }
    public function relatorio(Request $request)
    {

        $placa = ControleHorariosService::query($request);

        return response ([
            'placa' => $placa,
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
            $arrayExcel[] = array("PLACA","INÍCIO","FIM","TEMPO","CÓDIGO","EVENTO","LOCAL");
            foreach($arrayDados as $placa=>$placas){
                 foreach($placas as $data=>$linha){

                     $linha[5] = str_replace("<strong>","",$linha[5]);
                     $linha[5] = str_replace("</strong>","",$linha[5]);

                    $arrayExcel[] = array($linha[0],
                                          $linha[1],
                                          $linha[2],
                                          $linha[3],
                                          $linha[4],
                                          $linha[5],
                                          $linha[6]
                                        );
                 }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('Controle de Horários',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('Controle de Horários',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }

    public function dadosFiltros(Request $request){

        $veiculos = Veiculo::select('veplaca', 'vecodigo','veprefixo')
            ->where('vestatus','A')
            ->whereIn('veproprietario', $request->clientes)->get();

        $motoristas = Motorista::select('mtcodigo','mtnome')
            ->where('mtstatus','A')
            ->whereIn('mtcliente', $request->clientes)->get();

        return response ([
            'veiculos'=>$veiculos,
            'motoristas'=>$motoristas
        ]);
    }
}

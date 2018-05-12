<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Bilhete;
use App\Models\GrupoMotorista;
use App\Models\Motorista;
use App\Helpers\DataHelper;
use App\Helpers\PdfHelper;
use App\Services\KmPercorridoService;
use DB;
use PDF;
use Auth;
use Excel;

class KmPercorridoController extends Controller
{
    public function listar()
    {
        //$gmotoristas = GrupoMotorista::where('gmcliente', '=', Auth::user()->usucliente)->get();
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        $veiculos = Veiculo::where('veproprietario', '=', \Auth::user()->usucliente)
            ->where('vestatus', '=', 'A')
            ->get();

        return view('relatorios.kmsPercorridos.listar', compact('veiculos','clientes'));

    }

    public function relatorio(Request $request)
    {
        $placas = '';
        if(empty($request->clientes) && empty($request->buscar)) {
            return response([
                'dados' => ''
            ]);
        }
        $kmpService = new KmPercorridoService;

        $dados = $kmpService->query($request);
        return response([
            'dados' => $dados
        ]);
    }

    public function exportar(Request $request)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $dados = $request->dados;
        $dia = new \Datetime();
        $type = $request->type;

        if($type == 'pdf') {
            $dados = $request->dados['dados'];

            $request->request->add(['data_inicio' => $request->dados['data_inicio']]);
            $request->request->add(['data_fim' => $request->dados['data_fim']]);

            $nome = 'Quilômetros Percorridos';
            $th = 'Data, Prefixo, Descricao, Quilometragem percorrida';
            $pdf = new PdfHelper;
            $tempo = $pdf->geraPdf($request, $dados, $th, $nome);
            return response ([
                'dados' => $tempo,
            ]);
        }else{
            $dados = explode(';', $dados);
            $tempo = [];
            foreach($dados as $i => $dado) {
                $dado = trim($dado, '*i&');
                $tempo[$i] = explode('*i&', $dado);
            }
            return Excel::create('relatorio_km_percorrido', function($excel) use ($tempo) {
                $excel->sheet('mySheet', function($sheet) use ($tempo){
                    $sheet->fromArray($tempo);
                    $sheet->row(1, array(
                        'Data', 'Prefixo', 'Descricao', 'Quilometrage percorrida'
                    ));
                });
            })->download($type);
        }
    }

    public function buscarPlacasGrupoMotorista(Request $request){
      $gmcodigo = $request['gm_codigo'];

      $placas = DB::table('veiculos')
                  ->select('veiculos.veplaca', 'veiculos.vecodigo')
                  ->join('motoristas', 'motoristas.mtcodigo', '=', 'veiculos.vemotorista')
                  ->join('grupo_motorista', 'grupo_motorista.gmcodigo', '=', 'motoristas.mtgrupo')
                  ->where('motoristas.mtgrupo','=',$gmcodigo)
                  ->get();

      return response(['placas' => $placas]);
      // return response()->placas;

    }

    public function getVeiculosCliente(){
      $placas = Veiculo::select('veplaca','vecodigo')->where('veproprietario', '=', \Auth::user()->usucliente)
          ->get();

      return response(['placas' => $placas]);
      // return response()->placas;

    }
}

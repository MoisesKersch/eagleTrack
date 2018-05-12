<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\GrupoMotorista;
use App\Helpers\ExportaHelper;
use App\Models\Cliente;

class ExcessoVelocidadeController extends Controller
{
    public function listar()
    {
        ini_set('post_max_size','750MB');

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

        return view('relatorios.excessoVelocidade.listar', compact('clientes','veiculos','grupoMotorista'));
    }

    public function relatorio(Request $request)
    {

        $velos = $this->query($request);
        return response ([
            'dados' => $velos,
        ]);
    }

    public function todos(Request $request)
    {
        $idCli = Veiculo::select('vecodigo')
            ->join('clientes', 'clcodigo', '=', 'veproprietario')
            ->join('usuarios_clientes', 'clcodigo', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)->get();

        $id = '';
        foreach($idCli as $i){
            $id .= $i->vecodigo.',';
        }
        $request->buscar = trim($id, ',');

        $dados = $this->query($request);

        $clientes = Cliente::with('veiculos')
            ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)
            ->get();

        return response ([
            'dados' => $dados,
            'clientes' => $clientes,
        ]);
    }

    public function clientes(Request $request)
    {
        $id = $request->id;
        $placas = '';
        if(!empty($id))
            $placas = Veiculo::whereIn('veproprietario', $id)->get();

        return response ([
            'placas' => $placas,
        ]);
    }

    public function grupoMotorista(Request $request)
    {
        $clientes = $request->clientes;
        $grupoMotorista = new GrupoMotorista;
        $grupoMotorista = $grupoMotorista->getGrpMotorista($clientes);

        if (empty($clientes))
            $grupoMotorista = [];

        return response ([
            'grupoMotorista' => $grupoMotorista,
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
            $arrayExcel[] = array("Placa","Motorista","Data","Hora","Endereco","Vel. Permitida","Vel. Atingida","Vel. Excedida","% Excedida");
            foreach($arrayDados as $placa=>$placas){
                foreach($placas as $key=>$linha){
                    $arrayExcel[] = array($linha->placa,
                                          $linha->cmtnome,
                                          $linha->adata,
                                          $linha->bhora,
                                          $linha->endereco,
                                          $linha->evelmax,
                                          $linha->fbivelocidade,
                                          $linha->gexcedido,
                                          $linha->porcentagem
                                        );
                }
            }
            if($request->tipo == 'xls') $retorno = $arquivo->exportaExcel('TempoParado',$arrayExcel);
            if($request->tipo == 'csv') $retorno = $arquivo->exportaCSV('TempoParado',$arrayExcel);
            return response(['dados'=>$retorno]);
        }
    }

    public function query(Request $request)
    {
        $inicio = $request->data_inicio.' 00:00';
        $fim = $request->data_fim.' 23:59';
        if (gettype($request->buscar) == 'array') {
            $id = implode(", ", $request->buscar);
        } else {
            $id = $request->buscar;
        }

        $velos = \DB::table('veiculos')
            ->select(\DB::raw("vevelocidademax as evelmax, bidataevento as adata, bilatlog, biendereco, veprefixo,
                               coalesce(mtnome, 'Não Associado') as cmtnome, bivelocidade as fbivelocidade,veplaca as dveplaca, mtcodigo"))
            ->leftJoin('bilhetes', 'veplaca', '=', 'biplaca')
            ->leftJoin('motoristas', 'mtcodigo', '=', 'bimotorista')
            ->whereBetween('bidataevento', [$inicio,$fim])
            ->whereIn('bimotivotransmissao', [4, 21, 49, 57])
            ->whereRaw('bivelocidade > vevelocidademax');

        if($request->velocidade != '' && $request->velocidade != null){
            $velos->where('bivelocidade' ,'>=', $request->velocidade);
        }

        if (\Auth::user()->usumaster == 'N') $velos->where('veproprietario', '=', \Auth::user()->usucliente);
        if ($id) $velos->whereRaw('vecodigo in ('.$id.')');
        if ($request->gm) $velos->whereIn('mtgrupo', $request->gm);

        $velos->orderBy('veplaca', 'asc')
            ->orderBy('bidataevento', 'asc');

        $velos = $velos->get();
        if($velos->isEmpty())
            return [];

        //Efetua os cálculos
        foreach($velos as $i => $velo) {
            $data = new \DateTime($velo->adata);
            $velos[$i]->adata = $data->format('d/m/Y');
            $velos[$i]->bhora = $data->format('H:i:s');

            $latlong = explode(',',$velo->bilatlog);

            $velos[$i]->lat = $latlong[0];
            $velos[$i]->long = $latlong[1];
            $velos[$i]->biendereco = $velo->biendereco;

            //$velos[$i]->gexcedido = str_pad($velo->fbivelocidade - $velo->evelmax, 2, '0', STR_PAD_LEFT);
            $velos[$i]->gexcedido = $velo->fbivelocidade - $velo->evelmax;
            $velos[$i]->porcentagem = round(($velos[$i]->gexcedido * 100) / $velo->fbivelocidade, 2).' %';
            $array = (array)$velos[$i];
            ksort($array);
            $velos[$i] = $array;
        }

        $old = '';
        //Agrupa por placa
        $j = 0;
        foreach($velos as $i => $velo) {
            if($i == 0){
                 $old = $velo["dveplaca"];
            }

            if($velo["dveplaca"] == $old) {
                $placa[$velo["dveplaca"]][$j]['placa'] = $velo["dveplaca"];
                $placa[$velo["dveplaca"]][$j]['adata'] = $velo["adata"];
                $placa[$velo["dveplaca"]][$j]['bhora'] = $velo["bhora"];
                $placa[$velo["dveplaca"]][$j]['cmtnome'] = $velo["cmtnome"];
                $placa[$velo["dveplaca"]][$j]['lat'] = $velo["lat"];
                $placa[$velo["dveplaca"]][$j]['long'] = $velo["long"];
                $placa[$velo["dveplaca"]][$j]['endereco'] = $velo["biendereco"];
                $placa[$velo["dveplaca"]][$j]['evelmax'] = $velo["evelmax"];
                $placa[$velo["dveplaca"]][$j]['fbivelocidade'] = $velo["fbivelocidade"];
                $placa[$velo["dveplaca"]][$j]['gexcedido'] = $velo["gexcedido"];
                $placa[$velo["dveplaca"]][$j]['porcentagem'] = $velo["porcentagem"];
                $placa[$velo["dveplaca"]][$j]['veprefixo'] = $velo["veprefixo"];
                $j = $j + 1;
            }else{
                $j = 0;
            }
            $old = $velo["dveplaca"];
        }
        return $placa;
    }
}

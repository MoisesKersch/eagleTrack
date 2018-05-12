<?php

namespace App\Http\Controllers\Coletivos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use App\Models\Pontos;
use App\Models\Cliente;
use App\Models\Linha;
use App\Models\Regioes;
use App\Models\PontoLinha;
use App\Models\Horario;
use App\Helpers\RoteirizadorHelper;

class LinhasController extends Controller
{
    public function listagem()
    {
        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }
        return view('coletivos.cadastros.listagem', compact('clientes'));
    }
    public function listagemDados(Request $r)
    {
        // $linhas = $r->clientes;

        $linhas = Linha::with('cliente')->whereIn('licliente', $r->clientes)->get();

        return response([
            'linhas' => $linhas
        ]);
    }

    public function cadastro()
    {
        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }
        return view('coletivos.cadastros.cadastro', compact('clientes'));
    }
    public function editar($id)
    {
        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }

        $linha = Linha::with('pontosLinha')->find($id);
        // $pontos = PontoLinha::where('pllicodigo', $id)->get();
        $horarios = Horario::where('hrlicodigo', $id)->get();

        // $idsVeiculos = $perfil->veiculos()->pluck('veiculos.vecodigo')->toArray();
        $pontos = $linha->pontosLinha()->pluck('plpocodigo')->toArray();

        return view('coletivos.cadastros.cadastro', compact('clientes','linha','pontos','horarios'));
    }

    public function dadosEdicao(Request $r)
    {
        $linha = Linha::with('pontosLinha')->find($r->licodigo);
        // $pontos = PontoLinha::where('pllicodigo', $id)->get();
        $horarios = Horario::where('hrlicodigo', $r->licodigo)->get();

        // $idsVeiculos = $perfil->veiculos()->pluck('veiculos.vecodigo')->toArray();
        $pontos = $linha->pontosLinha()->pluck('plpocodigo')->toArray();
        $podados = [];
        foreach ($pontos as $i => $ponto) {
            array_push($podados,Pontos::find($ponto));
        }

        return response ([
            'horarios'=> $horarios,
            'pontos'=> $pontos,
            'podados'=> $podados
        ]);
    }

    public function salvar(Request $r){
        $linha = new Linha();
        $linha->lidescricao = $r->descricao;
        $linha->licliente = $r->cliente;
        $linha->liseguirordeminsercao = $r->seguirOrdemInsercaoPontos;
        if(!isset($r->licodigo)){
            $linha->save();
        }else{
            $linha->licodigo = $r->licodigo;
            PontoLinha::where('pllicodigo' , $r->licodigo)->delete();
            Horario::where('hrlicodigo' , $r->licodigo)->delete();
        }

        $count = 0;
        foreach ($r->pontos as $ponto) {
            $count++;
            $pontoLinha = new PontoLinha();
            $pontoLinha->pllicodigo = $linha->licodigo;
            $pontoLinha->plpocodigo = $ponto;
            $pontoLinha->plpoordem = $count;
            $pontoLinha->save();
        }

        foreach ($r->horarios as $hr) {
            $horario = new Horario();
            $horario->hrhorario = $hr[0];
            $horario->hrdiasemana = $hr[1];
            $horario->hrlicodigo = $linha->licodigo;
            $horario->save();
        }
        return response([
            'status' => 'ok'
        ]);
    }

    public function filtroPontos(Request $r){
        $pontos = Pontos::where('pocodigocliente', $r->clientes)->where('podescricao','ilike', "%$r->text%")->get();
        return response([
            'pontos' => $pontos
        ]);
    }

    public function todosPontos(Request $r){
        $pontos = Pontos::where('pocodigocliente', $r->cliente)->orderBy('podescricao','ASC')->get();
        return response([
            'pontos' => $pontos
        ]);
    }

    public function listaPontos(Request $r){
        if($r->pontos_selecionados == null){
            $r->pontos_selecionados = [];
        }
        $pontos = Pontos::where('pocodigocliente', $r->cliente)->whereIn('potipo', $r->tipos)->whereNotIn('pocodigo', $r->pontos_selecionados)->get();
        return response([
            'pontos' => $pontos
        ]);
    }

    public function listaRegioes(Request $r){
        $regioes = Regioes::with('regioesCoordenadas')->where('recliente', $r->cliente)->get();

        return response([
            'regioes' => $regioes
        ]);
    }

    public function rota(Request $r){
        $latLngs = array();
        foreach ($r->pontosSelecionados as $pocodigo) {
            if($pocodigo != null || $pocodigo != ''){
                $ponto = Pontos::find($pocodigo);
                array_push($latLngs,['polatitude'=>$ponto->polatitude, 'polongitude'=>$ponto->polongitude]);
            }
        }
        $rHelp = new RoteirizadorHelper();

        if($r->seguirOrdem == "true"){
            $rota = $rHelp->defineRotaRoute($latLngs, ['overview' => 'full','geometries' => 'polyline']);
        }else{
            $rota = $rHelp->defineRota($latLngs, ['overview' => 'full', 'roundtrip' => 'false','geometries' =>  'polyline', 'destination' => 'last', 'source' => 'first']);
        }

        return response([
            'rota' => $rota
        ]);
    }

    public function checkDescricao(Request $r){
        $contLinhas = Linha::where('licliente', $r->clientes)->where('lidescricao',$r->descricao)->count();

        return response([
            'cont_linhas' => $contLinhas
        ]);
    }

    public function excluir($id){
        PontoLinha::where('pllicodigo' , $id)->delete();
        Horario::where('hrlicodigo' , $id)->delete();
        Linha::where('licodigo' , $id)->delete();


        return redirect('painel/coletivos/cadastros/linhas/listagem')->with('message', 'Excluido com sucesso!');

    }

}

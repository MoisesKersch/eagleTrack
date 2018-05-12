<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TipoManutencao;
use App\Models\Cliente;
use App\Models\Veiculo as Veiculos;
use App\Models\ManutencaoProgramada;
use Validator;
use DB;

class ManutencaoController  extends Controller
{

    public function index(){
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clcodigo','clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.manutencao.listar')
            ->with('clientes', $clientes);
    }

    public function listar(Request $request){

        $clientes = $request->clientesbusca;
        $filtro = $request->filtro;
        $tipo_manutencao = $request->tipo_manutencao;

        $km_ate_manutencao = " ((coalesce(mapkmprogramado, 0)) - (coalesce(mohodometro, 0)/1000)) ";

        $m = ManutencaoProgramada::select('macodigo', 'clnome' , 'ticodigo','vecodigo','mapstatus','mapcliente','biplaca','veprefixo',
                'vedescricao','timdescricao','timkmpadrao', DB::raw('(coalesce(mohodometro, 0) / 1000) as mohodometro'),
                'mapkmprogramado','mapstatus',
                DB::raw(" $km_ate_manutencao as km_ate_manutencao"))
            ->join('tipo_manutencoes','ticodigo','=','maptipomanutencao')
            ->join('clientes','timproprietario','=','clcodigo')
            ->join('veiculos as v','vecodigo','=','mapcodigoveiculo')
            ->join('modulos', 'vemodulo', '=', 'mocodigo')
            ->join('bilhetes','biplaca','=','veplaca')
            ->whereIn('mapcliente', $clientes)
            ->where('bidataevento', '=', DB::raw('(select max(bidataevento) from bilhetes where biplaca = v.veplaca)'));

        if(!($tipo_manutencao == -1) && ($tipo_manutencao != null) ){
            $m = $m->where('ticodigo','=',$tipo_manutencao);
        }
        if($filtro == 'todas'){
            $m = $m->where('mapstatus','=','P');
        }elseif($filtro == 'proxima'){
            $m = $m->where( DB::raw($km_ate_manutencao) ,'<','1000');
            $m = $m->where( DB::raw($km_ate_manutencao) ,'>','0');
            $m = $m->where('mapstatus','=','P');
            $m = $m->orderBy('km_ate_manutencao','ASC');
            //TODO ordenar para o menor diferencia entre km aual e km programado
        }elseif($filtro == 'vencida'){
            //TODO buscar as vencidas
            $m = $m->where(DB::raw(" $km_ate_manutencao "),'<', 0 );
            $m = $m->where('mapstatus','=','P');
        }elseif($filtro == 'realizadas'){
            //Buscar todas com status P
            $m = $m->where('mapstatus','=','R');
        }

        $m = $m->get();

        // dd($m);

        return response([
            'manutencoes'=>$m
        ]);
    }

    public function cadastrar(){
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clcodigo','clnome')->get();
        }else{
            $ids = $this->getIdClientesUsuario(\Auth::user()->clientes);
            $clientes = Cliente::select('clcodigo','clnome')
                        ->whereIn('clcodigo', $ids)->get();
        }

        return view('cadastros.manutencao.cadastro', compact('clientes'));
    }

    public function salvar(Request $request){

        $dados = $request->all();

        $m = new ManutencaoProgramada();
        $m->mapcodigoveiculo = $dados["mapcodigoveiculo"];
        $m->maptipomanutencao = $dados["maptipomanutencao"];
        $m->mapusuario = \Auth::user()->id;

        date_default_timezone_set('America/Sao_Paulo');
        $m->mapdatahoralancamento = new \DateTime();
        $m->mapkmprogramado = $dados["kmProximaManutencao"];
        $m->mapcliente = $dados["mapcliente"][0];
        $m->mapstatus = "P";

        $m->save();

        return redirect('/painel/manutencao/manutencao')->with('success', 'Manutenção Salva!!!');
    }

    public function salvarNova(Request $request){

        $dados = $request->all();

        $m = new ManutencaoProgramada();

        $m->mapcodigoveiculo = $dados["veic_man"];
        $m->maptipomanutencao = $dados["ticodigo"];
        $m->mapusuario = \Auth::user()->id;

        date_default_timezone_set('America/Sao_Paulo');
        $m->mapdatahoralancamento = new \DateTime();
        $m->mapkmprogramado = $dados["km_proxima"];
        $m->mapcliente = $dados["cliente_man"];
        $m->mapstatus = "P";
        $m->save();

    }

    public function realizaManutencao(Request $request){

        $dados = $request->all();
        $m = ManutencaoProgramada::find($dados['id']);
        $m->mapkmrealizado = $dados['km_manu'];
        $m->mapstatus = 'R';
        $m->save();
    }

    public function editManutencao(Request $request){
        $dados = $request->all();
        $m = ManutencaoProgramada::find($dados['id']);
        $m->mapkmprogramado = $dados['km_edit'];
        $m->save();
    }

    // public function show($id){
    //     if(\Auth::user()->usumaster == 'S') {
    //         $clientes = Cliente::select('clcodigo', 'clnome')->get();
    //     }else {
    //         $clientes = \Auth::user()->clientes;
    //     }
    //
    //     $tim = TipoManutencao::with('cliente')->find($id);
    //
    //     return view('cadastros.tipoManutencao.cadastro', compact('tim', 'clientes'));
    // }
    //

    public function destroy(Request $request )
    {
      ManutencaoProgramada::destroy($request['id']);
    }

    public function tiposManutencaoUsuario(Request $request){

        if(!is_array($request->clientes)){
            $request->clientes = explode(' ',$request->clientes);
        }

        $tms = TipoManutencao::select('ticodigo','timdescricao','timkmpadrao')
            ->whereIn('timproprietario', $request->clientes )->get();

        $veiculos = Veiculos::select('vecodigo','vedescricao','veplaca','veprefixo')
            ->where('vestatus','A')
            ->whereIn('veproprietario', $request->clientes)->get();

        return response([
            'tipo_manutencao' => $tms,
            'veiculos' => $veiculos
        ]);
    }

    public function getIdClientesUsuario($clientes){
        $ids= [];
        foreach ($clientes as $key => $cliente) {
            array_push($ids,$cliente->clcodigo);
        }
        return $ids;
    }

}

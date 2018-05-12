<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use App\Services\PerfilService;
use App\Models\Perfil;
use App\Models\Cliente;
use App\Models\PerfilMenu;
use App\Models\Veiculo;
use App\Models\PerfilPermissoes;
use App\Models\PerfilItens;
use App\Models\GrupoVeiculo;
use App\Models\PerfilVeiculo;
use App\Helpers\CheckIdHelper;

class PerfilAcessoController extends Controller
{
    public function index()
    {
    	if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        return view('cadastros.perfilAcesso.listar', compact('clientes'));
    }

    public function listar(Request $request)
    {
    	$ids = $request->id;
        $perfis = Perfil::whereIn('pecliente', $ids)->with('empresa');

        if($request->status == 'A'){
            $perfis = $perfis->where('pestatus', true);
        }else if($request->status == 'I'){
            $perfis = $perfis->where('pestatus', false);
        }
    	return response ([
    		'perfis' => $perfis->get()
    	]);
    }

    public function criar(){
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clfantasia')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        $perfilMenus = PerfilMenu::all();
        return view('cadastros.perfilAcesso.cadastro', compact('clientes', 'perfilMenus'));
    }

    public function editar($id){
        if(!CheckIdHelper::checkId('perfis', 'pecodigo', 'pecliente', $id)) return redirect()->back();

        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clfantasia')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        // dd(\Auth::user()->perfil->veiculosNegados());
        $perfilMenus = PerfilMenu::all();
        $perfil = Perfil::with(['permissoes','veiculos' => function($query){
            $query->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : []);
        }])->find($id);

        // $idsVeiculos = $perfil->veiculos()->pluck('veiculos.vecodigo')->toArray();
        return view('cadastros.perfilAcesso.editar', compact('clientes', 'perfilMenus','perfil'));
    }

    public function save(Request $r){
        $dados_all = $r->all();
        $dados = array_diff_key($dados_all, ["ckveiculos" => "", "_token" => "", "pecliente" => "", "pedescricao" => "" , "pegrupoveiculo" => "", "peveiculos" => ""]);
        $orderMenus = PerfilService::agruparMenuItem($dados);
        $perfil = new Perfil();
        $perfil->pedescricao = $dados_all['pedescricao'];
        $perfil->pecliente = $dados_all['pecliente'];
        if(isset($dados_all['ckveiculos']) && $dados_all['ckveiculos'] == "on"){
            $perfil->peshowallveiculos = false;
        }else{
            $perfil->peshowallveiculos = true;
        }
        $perfil->save();

        if(isset($dados_all['ckveiculos']) && $dados_all['ckveiculos'] == "on"){
            //Salvar a relacao do perfil com veículos
            PerfilService::saveRelacaoPerfilVeiculo($perfil ,$dados_all);
        }
        PerfilService::saveRelacaoPerfilPermissoes($perfil, $orderMenus);

        return 200;
    }

    public function update(Request $r){
        $dados_all = $r->all();
        $dados = array_diff_key($dados_all, ["ckveiculos" => "", "_token" => "", "pecodigo" => "", "pedescricao" => "" , "pegrupoveiculo" => "", "peveiculos" => ""]);
        $orderMenus = PerfilService::agruparMenuItem($dados);

        $perfil = Perfil::find($dados_all['pecodigo']);
        $perfil->pedescricao = $dados_all['pedescricao'];
        if(isset($dados_all['ckveiculos']) && $dados_all['ckveiculos'] == "on"){
            $perfil->peshowallveiculos = false;
        }else{
            $perfil->peshowallveiculos = true;
        }
        $perfil->save();

        //Salvar a relacao do perfil com veículos
        PerfilService::removeOldRelacaoPerfilVeiculo($perfil);
        if(isset($dados_all['ckveiculos']) && $dados_all['ckveiculos'] == "on"){
            PerfilService::saveRelacaoPerfilVeiculo($perfil ,$dados_all);
        }

        //Salvar a relacao do perfil com permissoes
        PerfilService::removeOldRelacaoPerfilPermissoes($perfil);
        PerfilService::saveRelacaoPerfilPermissoes($perfil, $orderMenus);

        return 200;
    }


    public function ativar($id)
    {
        if(!CheckIdHelper::checkId('perfis', 'pecodigo', 'pecliente', $id)) return redirect()->back();
        DB::table('perfis')
            ->where('pecodigo', $id)
            ->update(['pestatus' => true]);
        return response ([
            'status' => 200
        ]);
    }

    public function desativar($id)
    {
        if(!CheckIdHelper::checkId('perfis', 'pecodigo', 'pecliente', $id)) return redirect()->back();
        DB::table('perfis')
            ->where('pecodigo', $id)
            ->update(['pestatus' => false]);
        return response ([
            'status' => 200
        ]);
    }

    public function perfilItens(Request $request)
    {
        $id = $request->id;
        $pecodigo = $request->pecodigo;
        $menu = Cliente::menusClienteSistema($id);
        $perfil = null;
        if($pecodigo != null){
            $perfil = Perfil::with('permissoes')->find($pecodigo);
        }
        return response ([
            'menu' => $menu,
            'perfil' => $perfil
        ]);
    }

    public function gruposVeiculos(Request $request)
    {
        $id = $request->id;
        $grupos = GrupoVeiculo::with('veiculos')->where('gvempresa', $id)->get();
        $veiculos = Veiculo::where('veproprietario', $id)->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])->get();
        $modulosSistema = (Cliente::find($id))->modulosSistema()->get();

        return response ([
            'grupos' => $grupos,
            'veiculos' => $veiculos,
            'modulosSistema' => $modulosSistema
        ]);
    }

    public function veiculosGrupo(Request $r){
        $ids = $r->ids;
        $veiculos = Veiculo::whereIn('vegrupoveiculo', $ids)->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])->get();
        return response ([
            'veiculos' => $veiculos
        ]);
    }
    public function checDescricao(Request $r){
        $desc = $r->desc;
        $status = false;
        $perfil = Perfil::where('pedescricao', '=', $desc)->where('pecliente',$r->empresa)->count();
        if($perfil > 0){
            $status = true;
        }
        return response ([
            'status' => $status
        ]);
    }
}

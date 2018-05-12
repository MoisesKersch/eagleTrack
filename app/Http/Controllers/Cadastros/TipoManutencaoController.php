<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TipoManutencao;
use App\Models\Cliente;
use Validator;
use DB;

class TipoManutencaoController  extends Controller
{

    private $rules = [
        'timdescricao'   => 'required',
        'timkmpadrao'    => 'required|numeric'
    ];


    public function index(){
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clcodigo','clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.tipoManutencao.listar')
            ->with('clientes', $clientes);
    }

    public function listar(Request $request){

        $tiposManutencao = TipoManutencao::whereIn('timproprietario', $request->clientesbusca)
                        ->join('clientes','clcodigo','=','timproprietario')
                        ->orderBy('timdescricao', 'ASC')
                        ->get();

        return response([
            'tipos_manutencao'=>$tiposManutencao
        ]);
    }

    public function cadastrar(){
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clcodigo','clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.tipoManutencao.cadastro')
            ->with('clientes', $clientes);
    }


    public function salvar(Request $request){

        $dados = $request->all();
        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if(!is_null($dados['ticodigo'])){
            $tm = TipoManutencao::find($dados['ticodigo']);
        }else{
            $tm = new TipoManutencao();
        }
        $tm->timdescricao = $dados["timdescricao"];
        $tm->timkmpadrao = $dados["timkmpadrao"];
        $tm->timproprietario = $dados["timproprietario"];

        $tm->save();

        return redirect('/painel/manutencao/tipo_manutencao')->with('success', 'Tipo Manutenção Salva!!!');
    }

    public function show($id){
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }

        $tim = TipoManutencao::with('cliente')->find($id);

        return view('cadastros.tipoManutencao.cadastro', compact('tim', 'clientes'));
    }

    public function destroy(Request $request)
    {
      TipoManutencao::destroy($request['id']);
    }

}

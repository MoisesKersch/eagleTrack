<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feriado;
use App\Models\Cliente;
use App\Helpers\CheckIdHelper;
use Auth;
use App\User;
use Validator;

class FeriadosController extends Controller
{
    private $rules = [
        'frdescricao' => 'required',
        'frdata' => 'required',
        'frcliente' => 'required',
        'frtipo' => 'required',
    ];

    public function index()
    {
    	if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }
    	return view('cadastros.feriados.index', compact('clientes'));
    }
    public function cadastro()
    {
        $user = User::with('clientes', 'cliente')->where('usucliente', '=', Auth::user()->usucliente)->first();
        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = $user->clientes;
        }

    	return view('cadastros.feriados.cadastro', compact('user', 'clientes'));
    }
    public function salvar(Request $request)
    {
        $dados = $request->all();
        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()){
           return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $feriado = new Feriado($dados);
        $feriado->frdata = \DateTime::createFromFormat('d/m/Y', $request->frdata.'/1900');
        if($request->frtipo == 'N'){
            $feriado->frcliente = 1;
        }
        $feriado->save();

        return redirect('painel/cadastros/feriados')->with('menssage', 'Salvo com sucesso!');
    }
    public function listagem(Request $request)
    {
        $id = $request->id;
        $tipo = $request->tipo;

        if(empty($id)){
            return response([
                'feriados' => [],
            ]);
        }
        $feriados = Feriado::with('cliente')
            ->where(function($query) use($id){
                $query->whereIn('frcliente', $id)
                ->orWhere('frtipo', 'N');
            });
            if(isset($tipo)){
                $feriados->where('frtipo', '=', $tipo);
            }
            $feriados = $feriados->get();
        return response([
            'feriados' => $feriados,
            'user' => \Auth::user()->usumaster,
        ]);
    }
    public function editar($id)
    {
        if(!CheckIdHelper::checkId('feriados', 'frcodigo', 'frcliente', $id)) return redirect()->back();

        $feriado = Feriado::find($id);

        $feriado->frdata = \DateTime::createFromFormat('Y-m-d', $feriado->frdata)->format('d/m');

        $user = User::with('clientes', 'cliente')->where('usucliente', '=', Auth::user()->usucliente)->first();
        if(Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $clientes = $user->clientes;
        }

        return view('cadastros.feriados.cadastro', compact('feriado', 'user', 'clientes'));
    }
    public function update(Request $request, $id)
    {
        $dados = $request->all();

        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $feriado = Feriado::find($id);
        $feriado->fill($dados);
        $feriado->frdata = \DateTime::createFromFormat('d/m/Y', $request->frdata.'/1900');
        if($request->frtipo == 'N'){
            $feriado->frcliente = 1;
        }
        $feriado->save();

        return redirect('painel/cadastros/feriados')->with('menssage', 'Editado com sucesso!');
    }
    public function excluir(Request $request, $id)
    {
        if(!CheckIdHelper::checkId('feriados', 'frcodigo', 'frcliente', $id)) return redirect()->back();
        $feriado = Feriado::find($id);
        $feriado->delete();

        return redirect('painel/cadastros/feriados')->with('message', 'Excluido com sucesso!');
    }
    public function duplicados(Request $request)
    {
        $data = $request->data;
        $data = \DateTime::createFromFormat('d/m/Y', $data.'/1900');
        $id = $request->id;

        $feriado = Feriado::where('frdata', '=', $data->format('d/m/Y'))
            ->where(function($query) use($id){
                $query->where('frcliente', $id)
                ->orWhere('frtipo', 'N');
            });
            // ->where('frcliente', '=', $id)->get();
            $feriado = $feriado->get();

        return $feriado;
    }
}

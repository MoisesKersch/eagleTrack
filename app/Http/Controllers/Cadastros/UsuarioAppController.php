<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UsuarioApp;
use App\Models\Cliente;
use App\Models\Motorista;
use Validator;
use App\User;
use DB;
use App\Models\ModuloModelo;
use App\Models\Modulo;
use App\Helpers\CheckIdHelper;

class UsuarioAppController extends Controller
{
    private $rules = [
        'usacliente' => 'required',
        'usaperfil' => 'required',
        //'usausuario' => 'required_if:',c ,
        'usastatus' => 'required',
        'usarastreador' => 'required',
    ];

    public function listar()
    {
        if(\Auth::user()->usumaster == 'S') {
            $usuarios = UsuarioApp::all();
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else {
            $usuarios = UsuarioApp::join('clientes', 'usacliente', '=', 'clcodigo')
                ->join('usuarios_clientes', 'usacliente', '=', 'clcodigo')
                ->where('uclusuario', '=', \Auth::user()->id)
                ->get();
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.usuariosApp.listar', compact('usuarios', 'clientes'));
    }
    public function criar()
    {
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }
        return view('cadastros.usuariosApp.cadastro', compact('clientes'));
    }

    public function associado(Request $request)
    {
        $val = $request->val;
        $cliente = $request->cliente;

        //todo verificar se já possui uma assossiacao
        if($val == 'M') {
            $dados = Motorista::select('mtcodigo as codigo', 'mtnome as nome')
                    ->whereNotIn('mtcodigo', function($q){
                        $q->select(DB::raw('coalesce(usamotorista,0)'))
                          ->from('usuario_apps');
                    })
                    ->where('mtstatus', '=', 'A')
                    ->where('mtcliente', '=', $cliente)->get();

        }elseif($val == 'R') {
            $dados = User::select('id as codigo', 'name as nome')
                    ->whereNotIn('id', function($q){
                        $q->select(DB::raw('coalesce(usausuario,0)'))
                            ->from('usuario_apps');
                    })
                    ->where('usuativo', '=', 'S')
                    ->where('usucliente', '=', $cliente)->get();
        }

        return response ([
            'dados' => $dados,
        ]);
    }
    public function cadastro(Request $request)
    {
        $dados = $request->all();
        if($request->usaperfil == 'M'){
            $this->rules['usamotorista'] =  'required';
        }elseif($request->usaperfil = 'R'){
            $this->rules['usausuario'] = 'required';
        }
        $modelo = ModuloModelo::where('mmdescricao', 'ilike', 'Smartphone%')->first();
        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $id = rand(0, 999999);
        $id = str_pad($id, 6, '1', STR_PAD_LEFT);
        $usuapp = new UsuarioApp($dados);
        $usuapp->usacodigo = $id;
        $usuapp->save();
        if($request->usarastreador == 'S') {
            $mod = new Modulo;
            $mod->mocodigo = $usuapp->usacodigo;
            $mod->mostatus = 'A';
            $mod->momodelo = $modelo->mmcodigo;
            $mod->moproprietario = 1;
            $mod->save();
        }
        return redirect('painel/cadastros/usuarios/app');
    }

    public function editar($id)
    {
        if(!CheckIdHelper::checkId('usuario_apps', 'usacodigo', 'usacliente', $id)) return redirect()->back();

        $usuapp = UsuarioApp::find($id);
         if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        }else {
            $clientes = \Auth::user()->clientes;
        }
        return view('cadastros.usuariosApp.cadastro', compact('clientes', 'usuapp'));
    }
    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();
        $usuapp = UsuarioApp::find($id);
        if($request->usaperfil == 'M'){
            $this->rules['usamotorista'] =  'required';
            $usuapp->usausuario = null;
        }elseif($request->usaperfil = 'R'){
            $this->rules['usausuario'] = 'required';
            $usuapp->usamotorista = null;
        }
        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $usuapp->fill($dados);
        $usuapp->save();

        return redirect('painel/cadastros/usuarios/app');
    }

    public function desativar($id)
    {
        if(!CheckIdHelper::checkId('usuario_apps', 'usacodigo', 'usacliente', $id)) return redirect()->back();
        $usuapp = UsuarioApp::find($id);
        return view('cadastros.usuariosApp.desativar', compact('usuapp'));
    }
    public function desable($id)
    {
        if(!CheckIdHelper::checkId('usuario_apps', 'usacodigo', 'usacliente', $id)) return redirect()->back();
        $usuapp = UsuarioApp::find($id);
        $usuapp->usastatus = 'I';
        $usuapp->save();
        //return redirect('painel/cadastros/usuarios/app');
        return response([
            'mensagem' => 'Alterado com sucesso!',
            'status' => '200'
        ]);
    }
    public function ativar($id)
    {
        if(!CheckIdHelper::checkId('usuario_apps', 'usacodigo', 'usacliente', $id)) return redirect()->back();
        $usuapp = UsuarioApp::find($id);
        $usuapp->usastatus = 'A';
        $usuapp->save();

        return response ([
            'mensagem' => 'Status Atualizado!!!',
            'status' => '200'
        ]);
    }

    public function destroy($id)
    {
        if(!CheckIdHelper::checkId('usuario_apps', 'usacodigo', 'usacliente', $id)) return redirect()->back();
        $usuapp = UsuarioApp::where('usausuario',$id)->get();
        foreach ($usuapp as $key => $value) {
            $usuapp->usausuario = null;
            $usuapp->save();
        }
        UsuarioApp::destroy($id);

        return response ([
            'mensagem' => 'Usuário Removido!!!',
            'status' => '200'
        ]);
    }

    public function status(Request $request)
    {
        $status = $request->val;
        $ids = $request->id;

        $usuapp = UsuarioApp::whereIn('usacliente', $ids);
        if($status != 'T'){
            $usuapp = $usuapp->where('usastatus', '=', $status);
        }

        $usuapp = $usuapp->with('usuario', 'motorista');
        $usuapp = $usuapp->get();

        return response([
            'status' => $usuapp
        ]);
    }

    public function dadosCliente(Request $request)
    {
        $clientes = '';

        $usuapp = UsuarioApp::whereIn('usacliente', $request->id);
        $usuapp = $usuapp->with('usuario', 'motorista');
        $usuapp = $usuapp->get();

        return response ([
            'clientes' => '',
            'status' => $usuapp
        ]);
    }
}

<?php
namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Cliente;
use App\Models\Perfil;
use App\Models\PerfilPermissoes;
use App\Helpers\AcessoHelper;
use Validator;
use App\Helpers\CheckIdHelper;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    private $rulesCad = [
        'name'       => 'required|unique:users,name',
        'email'      => 'required|unique:users,email',
        'password'   => 'required',
        'usuativo'   => 'required',
        'usucliente' => 'required',
        'usuperfil' => 'required'
    ];

    public function criar()
    {
        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->where('clstatus', '=', 'A')->get();
        }else{
            $clientes = Cliente::select('clnome', 'clcodigo')->where('clstatus', '=', 'A')->whereIn('clcodigo', \Auth::user()->getEmpresasUsuario())->get();
        }
        return view('cadastros.usuarios.cadastro', compact('clientes'));
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();

        $validator = Validator::make($dados, $this->rulesCad);
        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $usuario = new User($dados);
        $usuario->password = Hash::make($usuario->password);

        $usuario->save();
        $usuario->clientes()->attach($request->multcliente);

        return redirect('/painel/cadastros/usuarios')->with('success', 'Salvo com sucesso!!!');
    }

    public function editar($id)
    {
        if(!CheckIdHelper::checkId('users', 'id', 'usucliente', $id)) return redirect()->back();

        $usuario = User::with('clientes')->find($id);
        $clid = [];
        foreach($usuario->clientes as $cliente) {
            $clid[] = $cliente->clcodigo;
        }

        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->where('clstatus', '=', 'A')->get();
        }else{
            $clientes = Cliente::select('clnome', 'clcodigo')->where('clstatus', '=', 'A')->whereIn('clcodigo', \Auth::user()->getEmpresasUsuario())->get();
        }
        return view('cadastros.usuarios.editar', compact('usuario', 'clientes', 'clid'));
    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();
        $usuario = User::find($id);
        $validator = Validator::make($dados, [
            'id'         => 'nullable',
            'name'       => [
                                'required',
                                Rule::unique('users')->ignore($usuario->id, 'id'),
                            ],
            'password'   => 'nullable',
            'email'      => [
                                'required',
                                Rule::unique('users')->ignore($usuario->id, 'id'),
                            ],
            'usuativo'   => 'required',
            'usucliente' => 'required',
            'usuperfil' => 'required'
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $senhaAntiga = $usuario->password;
        $usuario->fill($dados);

        if($usuario->password == null) {
            $usuario->password = $senhaAntiga;
        } else {
            $usuario->password = Hash::make($usuario->password);
        }
        $usuario->save();
        $usuario->clientes()->sync($request->multcliente);

        return redirect('/painel/cadastros/usuarios')->with('success', 'Editado com sucesso!!!');
    }

    public function listar()
    {
        // @item usuarios -> 13
        // dd(\Auth::user()->perfil->pecodigo);
        // $permissoes = PerfilPermissoes::where('ppperfilitens', 13)->where('ppperfilcodigo', \Auth::user()->perfil->pecodigo)->get();

        // $permissoes = AcessoHelper::acessos('cadusuarios');
        // if(isset($permissoes)){
        //     dd($permissoes->ppvisualizar);
        // }else{
        //     dd($permissoes);
        // }

        // $permissoes = AcessoHelper::acessosPermissao('cadusuarios','ppvisualizar');
        // dd($permissoes);

        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clnome', 'clcodigo')->where('clstatus', '=', 'A')->get();
        }else{
            $clientes = Cliente::select('clnome', 'clcodigo')->where('clstatus', '=', 'A')->whereIn('clcodigo', \Auth::user()->getEmpresasUsuario())->get();
        }
        return view('cadastros.usuarios.listar', compact('clientes'));
    }

    public function alterarStatus($data)
    {
        if(!CheckIdHelper::checkId('users', 'id', 'usucliente', $data)) return redirect()->back();

        $usuario = User::find($data);

        if($usuario->usuativo == "N") {
            $usuario->usuativo = "S";
        } else if($usuario->usuativo == "S") {
            $usuario->usuativo = "N";
        }

        $usuario->save();

        return $usuario->usuativo;
    }

    public function alterarMaster($data)
    {
        $dados = explode('&', $data);

        $idUsuario = $dados[0];
        $usumaster = $dados[1];

        $usuario = User::find($idUsuario);

        if($usumaster == "N") {
            $usumaster = "S";
        } else if($usumaster == "S") {
            $usumaster = "N";
        }

        $usuario->usumaster = $usumaster;
        $usuario->save();

        return redirect()->back();
    }
    public function clientes(Request $request)
    {
        $user = new User;
        $id = $request->id;
        $usuarios = $user->getUsuarioCliente($id);

        return response ([
            'usuarios' => $usuarios,
            'nome' => \Auth::user()->name,
        ]);
    }

    public function perfis(Request $request)
    {
        if($request->empresas == null){
            return response ([
                'perfis' => null
            ]);
        }
        $ids = $request->empresas;
        $perfis = Perfil::select('pecodigo', 'pedescricao')->where('pestatus', true)->whereIn('pecliente',$ids)->get();
        return response ([
            'perfis' => $perfis
        ]);
    }

}

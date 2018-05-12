<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\GrupoMotorista;
use App\Models\Cliente;
use App\Helpers\CheckIdHelper;
use Auth;
use Validator;

class GruposMotoristasController extends Controller
{
    private $rules = [
        'descricao'    => 'required',
        'gmcliente'      => 'required',
        'gmstatus'       => 'required'
    ];

    public function listar(Request $request, $status = '')
    {
        $status = $request->status;
        $usuario = Auth::user();
        $clientes_busca = $usuario->usucliente;
        $codCliente = false;

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
            $adm = true;
        } else {
            $clientes = \Auth::user()->clientes;
            $adm = false;
            foreach ($clientes as $c) {
                $codCliente[] = $c->clcodigo;
            }

            $grupos = GrupoMotorista::whereIn('gmcliente', $codCliente ?: [$clientes_busca]);
            if ($status == 'ativo') {
                $grupos = $grupos->where('gmstatus', '=', 'A');
            } elseif ($status == 'inativo') {
                $grupos = $grupos->where('gmstatus', '=', 'I');
            }
        }

        $grupos = $adm ? [] : $grupos->get();

        return view('cadastros.gruposMotoristas.listar', compact('grupos', 'status', 'clientes', 'clientes_busca', 'adm'));
    }

    public function listarTable(Request $request, $status = '')
    {
        $status = $request->status;
        $clientesbusca = $request->clientesbusca;

        if (count($clientesbusca) == 0) {
            return [];
        }

        $grupos = GrupoMotorista::with('clienteGm');

        if (!in_array('T', $clientesbusca)) {
            $grupos->whereIn('gmcliente', $clientesbusca);
        }

        if (in_array('T', $clientesbusca) && \Auth::user()->usumaster == 'N') {
            $id = [];
            foreach (\Auth::user()->clientes as $value) {
                $id[] = $value->clcodigo;
            }

            $grupos->whereIn('gmcliente', $id);
        }

        if ($status == 'ativo') {
            $grupos = $grupos->where('gmstatus', '=', 'A');
        } elseif ($status == 'inativo') {
            $grupos = $grupos->where('gmstatus', '=', 'I');
        }

        return $grupos->get();
    }

    public function criar()
    {
        $usuario = Auth::user();
        $grupo =  $usuario->usucliente;

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.gruposMotoristas.criar', compact('grupo', 'clientes'));
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

        $grupo = new GrupoMotorista($dados);
        $grupo->gmdescricao = $dados['descricao'];
        $grupo->save();

        return redirect('painel/cadastros/gruposMotoristas')->with('success', 'Grupo de motoristas salvo!!!');
    }

    public function editar($id)
    {
        if(!CheckIdHelper::checkId('grupo_motorista', 'gmcodigo', 'gmcliente', $id)) return redirect()->back();

        $grupo = GrupoMotorista::find($id);

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.gruposMotoristas.editar', compact('grupo', 'clientes'));
    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();
        $validator = Validator::make($dados, $this->rules);

        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $grupo = GrupoMotorista::find($id);
        $grupo->gmdescricao = $dados['descricao'];
        $grupo->fill($dados);
        $grupo->save();

        return redirect('painel/cadastros/gruposMotoristas')->with('success', 'Grupo de motoristas editado!!!');
    }

    public function ativar(Request $request)
    {
        $id = $request->id;
        if(!CheckIdHelper::checkId('grupo_motorista', 'gmcodigo', 'gmcliente', $id)) return redirect()->back();

        $grupo = GrupoMotorista::find($id);
        $grupo->gmstatus = 'A';
        $grupo->save();

        return response ([
            'mensagem' => 'Atualizado com sucesso',
            'status' => '200'
        ]);
    }

    public function desativar($id)
    {
        if(!CheckIdHelper::checkId('grupo_motorista', 'gmcodigo', 'gmcliente', $id)) return redirect()->back();

        $grupo = GrupoMotorista::find($id);
        return view('cadastros.gruposMotoristas.desativar', compact('grupo'));
    }

    public function disable($id)
    {
        if(!CheckIdHelper::checkId('grupo_motorista', 'gmcodigo', 'gmcliente', $id)) return redirect()->back();

        $grupo = GrupoMotorista::find($id);
        $grupo->gmstatus = 'I';
        $grupo->save();

        return response([
            'mensagem' => 'Alterado com sucesso!',
            'status' => '200'
        ]);

        //return redirect('painel/cadastros/gruposMotoristas?status=ativo')->with('success', 'Grupo de motoristas desativado!!!');
    }
}

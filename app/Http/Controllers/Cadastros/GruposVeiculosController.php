<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\GrupoVeiculo;
use App\Models\Cliente;
use App\Models\Veiculo;
use Auth;
use Validator;
use App\Helpers\CheckIdHelper;

class GruposVeiculosController extends Controller
{
    private $rules = [
        'gvdescricao'    => 'required',
        'gvempresa'      => 'required',
        'gvstatus'       => 'required',
        'veiculos'       => 'required'
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

            $grupos = GrupoVeiculo::whereIn('gvempresa', $codCliente ?: [$clientes_busca]);
            if ($status == 'ativo') {
                $grupos = $grupos->where('gvstatus', '=', 'A');
            } elseif ($status == 'inativo') {
                $grupos = $grupos->where('gvstatus', '=', 'I');
            }
        }

        $grupos = $adm ? [] : $grupos->get();

        return view('cadastros.gruposVeiculos.listar', compact('grupos', 'status', 'clientes', 'clientes_busca', 'adm'));
    }

    public function listarTable(Request $request, $status = '')
    {
        $clientesbusca = $request->clientesbusca;
        if (count($clientesbusca) == 0) {
            return [];
        }
        $grupos = GrupoVeiculo::with('clienteGv','veiculos');

        if (!in_array('T', $clientesbusca)) {
            $grupos->whereIn('gvempresa', $clientesbusca);
        }

        if (in_array('T', $clientesbusca) && \Auth::user()->usumaster == 'N') {
            $id = [];
            foreach (\Auth::user()->clientes as $value) {
                $id[] = $value->clcodigo;
            }
            $grupos->whereIn('gvempresa', $id);
        }
        $grupos = $grupos->get();
        return $grupos;
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

        $veiculo = new Veiculo;
        $veiculos = $veiculo->getVeiculos([$grupo]);

        return view('cadastros.gruposVeiculos.criar', compact('grupo', 'clientes', 'veiculos'));
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

        if(!isset($dados['gvcodigo'])){
            $grupo = new GrupoVeiculo();
        }else{
            $grupo = GrupoVeiculo::find($dados['gvcodigo']);
            //Desvincular veículos do
            Veiculo::where('vegrupoveiculo', $grupo->gvcodigo)
                ->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])
                ->update(['vegrupoveiculo' => null]);
        }
        $grupo->fill($dados);
        $grupo->save();

        Veiculo::whereIn('vecodigo', $dados['veiculos'])
            ->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])
            ->update(['vegrupoveiculo' => $grupo->gvcodigo]);

        return redirect('painel/cadastros/gruposVeiculos')->with('success', 'Grupo de veículos salvo!!!');
    }

    public function editar($id)
    {
        if(!CheckIdHelper::checkId('grupo_veiculos', 'gvcodigo', 'gvempresa', $id)) return redirect()->back();

        $grupo = GrupoVeiculo::find($id);

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }
        $veiculo = new Veiculo;
        $veiculos = $veiculo->getVeiculos([$grupo->gvempresa]);

        return view('cadastros.gruposVeiculos.editar', compact('grupo', 'clientes','veiculos'));
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

        $grupo = GrupoVeiculo::find($id);
        $grupo->fill($dados);
        $grupo->save();

        return redirect('painel/cadastros/gruposVeiculos')->with('success', 'Grupo de veículos editado!!!');
    }

    public function delete($id)
    {
        if(!CheckIdHelper::checkId('grupo_veiculos', 'gvcodigo', 'gvempresa', $id)) return redirect()->back();

        GrupoVeiculo::destroy($id);

        return response ([
            'mensagem' => 'Removido com sucesso',
            'status' => '200'
        ]);
    }

    public function getVeiculos(Request $request)
    {
        $veiculo = new Veiculo;
        $veiculos = $veiculo->getVeiculos([$request->cliente]);

        return $veiculos;
    }

    public function checkVeiculo(Request $request)
    {
        if($request->gvcodigo != null){
            $v = Veiculo::with('grupo')->where('vecodigo',$request->vecodigo)->where('vegrupoveiculo','!=',$request->gvcodigo)->whereNotNull('vegrupoveiculo')->first();
        }else{
            $v = Veiculo::with('grupo')->where('vecodigo',$request->vecodigo)->whereNotNull('vegrupoveiculo')->first();
        }
        return response([
            'data' => $v
        ]);
    }

    public function desassociarVeiculoGrupo(Request $request)
    {
        $veic = Veiculo::where('vecodigo', $request->vecodigo)
            ->update(['vegrupoveiculo' => null]);


        return response([
            'mensagem' => 'Desassociado com sucesso!',
            'status' => '200'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\Regioes;
use App\Models\RegioesCoordenadas;
use App\Models\Cliente;
use App\Helpers\CheckIdHelper;
use App\Models\Pontos;
use App\Helpers\MapaHelper;
use Auth;
use Validator;
use DB;

class RegioesController extends Controller
{
    private $rules = [
        'redescricao' => 'required',
        'recliente' => 'required',
        'recoordenadas' => 'required|array|min:3',
        'revelocidade' => 'digits_between:0,3'
    ];

    public function listar(Request $request)
    {
        $usuario = Auth::user();
        $codcliente = $usuario->usucliente;

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo', 'cllongitude', 'cllatitude')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.regioes.listar', compact('clientes', 'codcliente', 'regioes'));
    }

    public function buscaRegioes(Request $request)
    {
        $multiempresa = $request->multiempresa;
        $regioes = Regioes::with('regioesCoordenadas');

        $clientes = json_decode($request->cliente);
        if(!is_array($clientes)){
            $clientes = (array) $clientes;
        }

        $regioes->select(DB::raw('cllatitude, cllongitude, recodigo, redescricao, revelocidade,
                    recor'))
                ->join('clientes', 'clientes.clcodigo', '=', 'regioes.recliente')
                ->whereIn('recliente',$clientes);
        $regioes = $regioes->get();

        if(count($regioes) == 0){
            $cliente = Cliente::select('cllatitude','cllongitude')
                ->whereIn('clcodigo',$clientes)
                ->get();

            return json_encode($cliente);
        }

        return json_encode($regioes);
    }

    public function salvar(Request $request)
    {
        $dados = [
            'recliente' => $request['params']['recliente'],
            'redescricao' => $request['params']['redescricao'],
            'recoordenadas' => json_decode($request['params']['recoordenadas'], true),
            'revelocidade' => $request['params']['revelocidade'],
            'recor' => $request['params']['recor']
        ];
        $validator = Validator::make($dados, $this->rules);

        if ($validator->fails()) return ['erro' => $validator->errors()];

        $regiao = new Regioes($dados);
        $regiao->save();
        $regiaoCoord = new RegioesCoordenadas();

        // $existe = $mapa->pointInPolygon($ponto, $regiao->regioesCoordenadas, $pointOnVertex = true);
        $regiaoCoord->salvarCoordenadasRegiao($dados['recoordenadas'], $regiao->recodigo);

        $mapa = new MapaHelper;
        $pontos = Pontos::where('pocodigocliente', '=', $regiao->recliente)->get();

        $pts = '';
        $pt = '';
        foreach ($pontos as $i => $ponto) {
            $pt['lat'] = $ponto->polatitude;
            $pt['log'] = $ponto->polongitude;
            $retorno = $mapa->pointInPolygon($pt, $dados['recoordenadas'], $pointOnVertex = true);
            if($retorno == 'inside'){
                $pts[] = $ponto->pocodigo;
            }
        }
        if(!empty($pts)){
            Pontos::whereIn('pocodigo', $pts)
                ->update(['poregiao' => $regiao->recodigo]);
        }
        return $regiao->recodigo;
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

        $jornadaTrabalho = JornadaTrabalho::find($id);
        $jornadaTrabalho->jtdescricao = $dados['descricao'];
        $jornadaTrabalho->fill($dados);
        $jornadaTrabalho->save();
        $jtcodigo = $jornadaTrabalho->jtcodigo;

        $hjtcodigos = '';
        foreach ($dados['horarios'] as $k => $v) {
            if (count($v) == 5) {
                $horasJornadaTrabalho = HorasJornadaTrabalho::find($v['hjtcodigo']);
            }
            elseif (count($v) == 1) {
                HorasJornadaTrabalho::find($v['hjtcodigo'])->delete();
            }
            else {
                $horasJornadaTrabalho = new HorasJornadaTrabalho();
            }
            $horasJornadaTrabalho->hjtiniprimeirot = $v['hjtiniprimeirot'];
            $horasJornadaTrabalho->hjtfimprimeirot = $v['hjtfimprimeirot'];
            $horasJornadaTrabalho->hjtinisegundot = $v['hjtinisegundot'];
            $horasJornadaTrabalho->hjtfimsegundot = $v['hjtfimsegundot'];
            $horasJornadaTrabalho->hjtdiasemana = $k;
            $horasJornadaTrabalho->hjtjornada = $jtcodigo;
            $horasJornadaTrabalho->save();
        }

        return redirect('painel/cadastros/jornadaTrabalho')->with('success', 'Jornada de trabalho editada!!!');
    }

    public function excluirRegiao(Request $request)
    {
        $regiao = $request->id;
        if(!CheckIdHelper::checkId('regioes', 'recodigo', 'recliente', $regiao)) return redirect()->back();

        try {
            Regioes::destroy($regiao);
            return 'true';
        } catch(\exception $e) {
            return 'false';
        }
    }
}

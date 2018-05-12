<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\JornadaTrabalho;
use App\Models\HorasJornadaTrabalho;
use App\Models\Cliente;
use App\Helpers\CheckIdHelper;
use Auth;
use Validator;

class JornadaTrabalhoController extends Controller
{
    // private $rules = [
    //     'descrição' => 'required',
    //     'jtcliente' => 'required',
    //     'jtstatus' => 'required',
    //     'horarios' => 'required',
    //     'horarios.*.hjtiniprimeirot' => 'required',
    //     'horarios.*.hjtfimprimeirot' => 'required',
    //     'horarios.*.hjtinisegundot' => 'required_with:horarios.*.hjtfimsegundot',
    //     'horarios.*.hjtfimsegundot' => 'required_with:horarios.*.hjtinisegundot'
    // ];

    public function listar(Request $request, $status = '')
    {
        $status = $request->status;
        $dias = $this->diasSemana();
        $usuario = Auth::user();
        $clientes_busca = ($request->clientesbusca ?: $usuario->usucliente);
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

            $jt = JornadaTrabalho::with(['horasJornadaTrabalho' => function($query) {
                $query->orderBy('hjtdiasemana', 'asc');
            }])
            ->with('clienteJornada')->whereIn('jtcliente', $codCliente ?: [$usuario->usucliente]);

            if ($status == 'ativo') {
                $jt = $jt->where('jtstatus', '=', 'A');
            } elseif ($status == 'inativo') {
                $jt = $jt->where('jtstatus', '=', 'I');
            }
        }

        $jt = $adm ? [] : $jt->get();

        return view('cadastros.jornadaTrabalho.listar', compact('jt', 'status', 'clientes', 'dias', 'clientes_busca', 'adm'));
    }

    public function listarTable(Request $request, $status = '')
    {
        $status = $request->status;
        $clientesbusca = $request->clientesbusca;

        if (count($clientesbusca) == 0) {
            return [];
        }

        $jt = JornadaTrabalho::with(['horasJornadaTrabalho' => function($query) {
            $query->orderBy('hjtdiasemana', 'asc');
        }])->with('clienteJornada');

        if (!in_array('T', $clientesbusca)) {
            $jt->whereIn('jtcliente', $clientesbusca);
        }

        if (in_array('T', $clientesbusca) && \Auth::user()->usumaster == 'N') {
            $id = [];
            foreach (\Auth::user()->clientes as $value) {
                $id[] = $value->clcodigo;
            }

            $jt->whereIn('jtcliente', $id);
        }

        if ($status == 'ativo') {
            $jt = $jt->where('jtstatus', '=', 'A');
        } elseif ($status == 'inativo') {
            $jt = $jt->where('jtstatus', '=', 'I');
        }

        return $jt->get();
    }

    public function criar()
    {
        $usuario = Auth::user();
        $cliente =  $usuario->usucliente;
        $dias = $this->diasSemana();

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }

        return view('cadastros.jornadaTrabalho.criar', compact('cliente', 'clientes', 'dias'));
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();
        $rules = [
            'descrição' => 'required',
            'jtcliente' => 'required',
            'jtstatus' => 'required',

        ];
        if($request->jttipo == 'L'){
            $plasRules = [
                'horario.*.hjttotalhoras' => 'required',
                'horario.*.hjtintervalo' => 'required',
                'horario' => 'required',
            ];
        }elseif($request->jttipo == 'F'){
            $plasRules = [
                'horarios' => 'required',
                'horarios.*.hjtiniprimeirot' => 'required',
                'horarios.*.hjtfimprimeirot' => 'required',
                'horarios.*.hjtinisegundot' => 'required_with:horarios.*.hjtfimsegundot',
                'horarios.*.hjtfimsegundot' => 'required_with:horarios.*.hjtinisegundot'
            ];
        }
        $rules = array_merge($rules, $plasRules);
        $validator = Validator::make($dados, $rules);

        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $jornadaTrabalho = new JornadaTrabalho($dados);
        $jornadaTrabalho->jtdescricao = $dados['descrição'];
        $jornadaTrabalho->jttipo = $dados['jttipo'];
        $jornadaTrabalho->jtdsr = $dados['rd-dsr'];

        $jornadaTrabalho->save();

        $hjtcodigos = '';
        if(isset($request->horarios)){
            $dias = 6;
            $dsr = 0;
            for ($i=0; $i < 7; $i++) {
                if(!array_key_exists($i, $request->horarios)){
                    $dsr = $i;
                    break;
                }
            }
            foreach ($dados['horarios'] as $k => $v) {
                $horasJornadaTrabalho = new HorasJornadaTrabalho();
                $horasJornadaTrabalho->hjtiniprimeirot = $v['hjtiniprimeirot'];
                $horasJornadaTrabalho->hjtfimprimeirot = $v['hjtfimprimeirot'];
                $horasJornadaTrabalho->hjtinisegundot = $v['hjtinisegundot'];
                $horasJornadaTrabalho->hjtfimsegundot = $v['hjtfimsegundot'];
                $horasJornadaTrabalho->hjtdiasemana = $k;
                // $horasJornadaTrabalho->hjtdsr = $dsr;
                $horasJornadaTrabalho->hjtjornada = $jornadaTrabalho->jtcodigo;
                $horasJornadaTrabalho->save();
            }
        }else{
            foreach ($dados['horario'] as $k => $v) {
                $horasJornadaTrabalho = new HorasJornadaTrabalho();
                $horasJornadaTrabalho->hjttotalhoras = $v['hjttotalhoras'];
                $horasJornadaTrabalho->hjtintervalo = $v['hjtintervalo'];
                $horasJornadaTrabalho->hjtdiasemana = $k;
                // $horasJornadaTrabalho->hjtdsr = $dsr;
                $horasJornadaTrabalho->hjtjornada = $jornadaTrabalho->jtcodigo;
                $horasJornadaTrabalho->save();
            }
        }

        return redirect('painel/cadastros/jornadaTrabalho')->with('success', 'Jornada de trabalho salvo!!!');
    }

    public function editar($id)
    {
        if(!CheckIdHelper::checkId('jornada_trabalho', 'jtcodigo', 'jtcliente', $id)) return redirect()->back();

        $jt = JornadaTrabalho::with('horasJornadaTrabalho')->find($id);

        $hjt = [];
        $lHjt = [];
        if($jt->jttipo == 'F'){
            foreach ($jt->horasJornadaTrabalho as $key => $value) {
                $hjt[$key] = $value;
            }
        }else{
            foreach ($jt->horasJornadaTrabalho as $key => $value) {
                $lHjt[$value->hjtdiasemana] = $value;
            }
        }

        usort($hjt, function($a, $b) {
            return $a->hjtdiasemana - $b->hjtdiasemana;
        });
        $dias = $this->diasSemana();

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clnome', 'clcodigo')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }
        $cliente = \Auth::user()->usucliente;

        return view('cadastros.jornadaTrabalho.editar', compact('jt', 'cliente', 'clientes', 'dias', 'hjt', 'lHjt'));
    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();
        $jornadaTrabalho = JornadaTrabalho::find($id);

        $deletaHorarios = [];
        $rules = [
            'descrição' => 'required',
            'jtcliente' => 'required',
            'jtstatus' => 'required',

        ];
        if($request->jttipo == 'L'){
            $plasRules = [
                'horario.*.hjttotalhoras' => 'required',
                'horario.*.hjtintervalo' => 'required',
                'horario' => 'required',
            ];
        }elseif($request->jttipo == 'F'){
            $plasRules = [
                'horarios' => 'required',
                'horarios.*.hjtiniprimeirot' => 'required',
                'horarios.*.hjtfimprimeirot' => 'required',
                'horarios.*.hjtinisegundot' => 'required_with:horarios.*.hjtfimsegundot',
                'horarios.*.hjtfimsegundot' => 'required_with:horarios.*.hjtinisegundot'
            ];
        }

        if (array_key_exists('horarios', $dados)) {
            foreach ($dados['horarios'] as $k => $v) {
                if (count($v) == 1) {
                    $deletaHorarios[] = $v['hjtcodigo'];
                    unset($dados['horarios'][$k]);
                }
            }
            if($jornadaTrabalho->jttipo == 'L'){
                $fixa = $jornadaTrabalho->horasJornadaTrabalho;
                foreach ($fixa as $k => $fix) {
                    $fix->delete();
                }
            }
        }
        if (array_key_exists('horario', $dados)) {
            foreach ($dados['horario'] as $k => $v) {
                if (count($v) == 1) {
                    $deletaHorarios[] = $v['hjtcodigo'];
                    unset($dados['horario'][$k]);
                }
            }
            if($jornadaTrabalho->jttipo == 'F'){
                $fixa = $jornadaTrabalho->horasJornadaTrabalho;
                foreach ($fixa as $k => $fix) {
                    $fix->delete();
                }
            }
        }

        $validator = Validator::make($dados, $rules);

        //deleta os horários não usados
        HorasJornadaTrabalho::destroy($deletaHorarios);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $jornadaTrabalho->jtdescricao = $dados['descrição'];
        $jornadaTrabalho->fill($dados);

        $jornadaTrabalho->jtdsr = $dados['rd-dsr'];
        $jornadaTrabalho->save();
        $jtcodigo = $jornadaTrabalho->jtcodigo;

        $hjtcodigos = '';
        if(isset($dados['horarios'])){
            foreach ($dados['horarios'] as $k => $v) {
                if (count($v) == 5) {
                    $horasJornadaTrabalho = HorasJornadaTrabalho::find($v['hjtcodigo']);
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
        }else if(isset($dados['horario'])){
            foreach ($dados['horario'] as $i => $hor) {
               if (count($hor) == 3) {
                    $horasJornadaTrabalho = HorasJornadaTrabalho::find($hor['hjtcodigo']);
                }else {
                    $horasJornadaTrabalho = new HorasJornadaTrabalho();
                }
                $horasJornadaTrabalho->hjttotalhoras = $hor['hjttotalhoras'];
                $horasJornadaTrabalho->hjtintervalo = $hor['hjtintervalo'];
                $horasJornadaTrabalho->hjtdiasemana = $i;
                $horasJornadaTrabalho->hjtjornada = $jornadaTrabalho->jtcodigo;
                $horasJornadaTrabalho->save();
            }
        }

        return redirect('painel/cadastros/jornadaTrabalho')->with('success', 'Jornada de trabalho editada!!!');
    }

    public function ativar(Request $request)
    {
        if(!CheckIdHelper::checkId('jornada_trabalho', 'jtcodigo', 'jtcliente', $id)) return redirect()->back();

        $id = $request->id;
        $jornadaTrabalho = JornadaTrabalho::find($id);
        $jornadaTrabalho->jtstatus = 'A';
        $jornadaTrabalho->save();

        return response ([
            'mensagem' => 'Atualizado com sucesso',
            'status' => '200'
        ]);
    }

    public function desativar($id)
    {
        if(!CheckIdHelper::checkId('jornada_trabalho', 'jtcodigo', 'jtcliente', $id)) return redirect()->back();

        $jt = JornadaTrabalho::find($id);
        return view('cadastros.jornadaTrabalho.desativar', compact('jt'));
    }

    public function disable($id)
    {
        if(!CheckIdHelper::checkId('jornada_trabalho', 'jtcodigo', 'jtcliente', $id)) return redirect()->back();

        $jornadaTrabalho = JornadaTrabalho::find($id);
        $jornadaTrabalho->jtstatus = 'I';
        $jornadaTrabalho->save();

        return response([
            'mensagem' => 'Alterado com sucesso!',
            'status' => '200'
        ]);

        // return redirect('painel/cadastros/jornadaTrabalho?status=ativo')->with('success', 'Jornada de trabalho desativada!!!');
    }

    private function diasSemana()
    {
        return [
            'Domingo',
            'Segunda',
            'Terça',
            'Quarta',
            'Quinta',
            'Sexta',
            'Sábado',
            'Feriado'
        ];
    }
}

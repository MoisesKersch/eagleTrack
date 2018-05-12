<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Validator;
use Auth;
use Excel;
use App\Models\Modulo;
use App\Models\Cliente;
use App\Models\ModuloModelo;
use App\Models\Chip;
use Illuminate\Validation\Rule;

class ModulosController extends Controller
{
    private $rules = [
        'mocodigo'         => 'required|unique:modulos,mocodigo',
        'moimei'           => 'required|unique:modulos,moimei',
        'modatainstalacao' => 'nullable',
        'mosim'            => 'nullable|unique:modulos,mosim',
        'momodelo'         => 'required',
        'moproprietario'   => 'required',
        'mostatus'         => 'required'
    ];


    public function criar()
    {
        $chips = Chip::where('chstatus','A')->get();
        $modulos = Modulo::whereNotNull('mosim')->get();

        foreach ($chips as $keyChip => $chip) {
            foreach ($modulos as $modulo) {
                if($chip->chcodigo == $modulo->mosim){
                    unset($chips[$keyChip]);
                }
            }
        }

        $modelos = ModuloModelo::all();

        $clientes = Cliente::where('clstatus','A')->get();

        return view('cadastros.modulos.cadastro',compact('chips','modelos','clientes'));
    }



    public function listar(Request $request)
    {

        $status = $request->status;
        $chip = $request->chip;
        $clientesbusca = $request->clientesbusca;

        $clientes_busca = $clientesbusca;

        $modulos = $this->buscarModulos($request);

        $clientes = Cliente::where('clstatus','A')->get();

        return view('cadastros.modulos.listar', compact('chip', 'status', 'clientes', 'clientes_busca'));
    }

    public function reload(Request $request)
    {
        $modulos = $this->buscarModulos($request);

        return response([
            'modulos' => $modulos
        ]);
    }

    private function buscarModulos(Request $request)
    {
        $status = $request->status;
        $chip = $request->chip;
        $clientesbusca = $request->clientesbusca;

        $modulos = Modulo::with('moduloModelo', 'chip', 'proprietario');

        if($clientesbusca != null && $clientesbusca != 'Selecione'){
            $modulos->whereIn('moproprietario', $clientesbusca);
        }elseif(Auth::user()->usumaster == 'S' && ($clientesbusca == null || $clientesbusca == 'Selecione')){
            $modulos->whereNotNull('moproprietario');
        }else{
            $modulos->where('moproprietario', '=', Auth::user()->usucliente);
        }

        if($chip == 'com') {
            $modulos->whereNotNull('mosim');
        } elseif($chip == 'sem') {
            $modulos->whereNull('mosim');
        }

        if($status == 'ativo') {
            $modulos->where('mostatus', '=', 'A');
        } elseif($status == 'inativo') {
            $modulos->where('mostatus', '=', 'D');
        } elseif($status == "todos") {
            $modulos->whereNotNull('mostatus');
        }

        $modulos = $modulos->get();

        $json_modulos = json_encode($modulos);
        $m = json_decode($json_modulos);

        $modulos = [];
        foreach ($m as $key => $array_modulo) {
            if($array_modulo->momodelo != null){
                $array_modulo->momodelo = $array_modulo->modulo_modelo->mmdescricao;
            }
            if($array_modulo->chip != null){
                $array_modulo->mosim = $array_modulo->chip->chnumero;
            }
            if($array_modulo->proprietario != null){
                $array_modulo->moproprietario = $array_modulo->proprietario->clnome;
            }
            unset($array_modulo->chip);
            unset($array_modulo->proprietario);
            unset($array_modulo->modulo_modelo);
            unset($array_modulo->moultimoevento);
            unset($array_modulo->moultimavelocidade);
            unset($array_modulo->moultimadirecao);
            unset($array_modulo->moultimaignicao);
            unset($array_modulo->moultimalon);
            unset($array_modulo->moultimalat);
            unset($array_modulo->modatainstalacao);
            unset($array_modulo->updated_at);
            unset($array_modulo->created_at);
            $modulos[$key] = $array_modulo;
        }

        return $modulos;
    }

    public function salvar(Request $request)
    {
        $dados = $request->all();

        if($dados["moproprietario"] == "Selecione..." || $dados["moproprietario"] == "Selecione..." ){
          return redirect()->back()
              ->with('error','Preencha todos os campos obrigatórios!')
              ->withInput($dados);
        }

        if($dados["mosim"] == "Selecione..." ){
            $dados["mosim"] = null;
        }

        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($dados);
        }

        $modulo = new Modulo($dados);

        $modulo->save();

        return redirect('/painel/cadastros/modulos')->with('successs', 'Salvo com sucesso!!!');
    }

    public function editar($id)
    {
        try {
            $modulo = Modulo::leftJoin('chips','mosim','=','chcodigo')
                ->leftJoin('veiculos','mocodigo','=','vemodulo')
                ->leftJoin('clientes','clcodigo','=','veproprietario')
                ->leftJoin('modulo_modelos','momodelo','=','mmcodigo')
                ->where('mocodigo', '=', $id)
                ->first();

            $chips = Chip::where('chstatus','A')->get();
            $modulos = Modulo::whereNotNull('mosim')->get();


            foreach ($chips as $keyChip => $chip) {
                foreach ($modulos as $m) {
                    if($modulo->chip != null){
                        if($chip->chcodigo == $m->mosim && $modulo->chip->chcodigo != $m->mosim){
                            unset($chips[$keyChip]);
                        }
                    }
                }
            }
        } catch (\Exception $e) { return back(); }

        $modelos = ModuloModelo::all();
        $clientes = Cliente::where('clstatus','A')->get();
        return view('cadastros.modulos.editar', compact('modulo','chips','modelos','clientes'));

    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();

        $validator = Validator::make($dados, [
            'modatainstalacao' => 'nullable',
            'moimei'           => 'required|unique:modulos,moimei,'.$dados['mocodigo'].',mocodigo',
            'mosim'            => 'nullable',
            'momodelo'         => 'required',
            'moproprietario'   => 'required',
            'mostatus'         => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $modulo = Modulo::find($id);
        $modulo->fill($dados);
        $modulo->save();

        return redirect('/painel/cadastros/modulos')->with('success', 'Editado com sucesso!!!');
    }

    public function alterarStatus(Request $request)
    {
        $mocodigo = $request->mocodigo;
        $modulo = Modulo::find($mocodigo);

        if($modulo->mostatus == "A") {
            $modulo->mostatus = "D";
            $status = "D";
        } else if ($modulo->mostatus == "D") {
            $modulo->mostatus = "A";
            $status = "A";
        }

        $modulo->save();

        return $status;
    }

    public function buscarModelo(Request $request)
    {
        $busca = $request->busca;

        $modelos = ModuloModelo::where('mmdescricao', 'ILIKE', '%'.trim($busca).'%')->get();

        return response([
            'dados' => $modelos,
        ]);
    }

    public function buscarSIM(Request $request)
    {
        $busca = $request->busca;

        $sim = Chip::where('chnumero', 'ILIKE', '%'.trim($busca).'%')->where('chstatus', '=', 'A')->get();

        $modulos = Modulo::whereNotNull('mosim')->get();

        foreach ($sim as $keyChip => $chip) {
            foreach ($modulos as $keyModulo => $modulo) {
                if($chip->chcodigo == $modulo->mosim){
                    $sim1 = $sim;
                    unset($sim[$keyChip]);
                }
            }
        }

        return response([
            'dados' => $sim
        ]);
    }

    public function exportar(Request $request)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $dia = new \Datetime();
        $type = $request->type;
        $dados = $this->buscarModulos($request);

        if($type == 'pdf'){
            $pdf = \PDF::loadView('cadastros.modulos.pdf', compact('dados'));
            return $pdf->stream();
        }else{
            $dados = json_decode($dados,true);

            foreach ($dados as $i => $d) {
                unset($dados[$i]['created_at']);
                unset($dados[$i]['updated_at']);
                unset($dados[$i]['moultimalat']);
                unset($dados[$i]['moultimalon']);
                unset($dados[$i]['moultimaignicao']);
                unset($dados[$i]['moultimadirecao']);
                unset($dados[$i]['moultimavelocidade']);
                unset($dados[$i]['moultimoevento']);
                unset($dados[$i]['mosim']);
                unset($dados[$i]['momodelo']);
                unset($dados[$i]['moproprietario']);

                if($dados[$i]['chip']){
                    $dados[$i]['chip'] =  $d['chip']['chnumero'];
                }else{
                    $dados[$i]['chip'] =  "sem chip";
                }

                if($dados[$i]['modulo_modelo']){
                    $dados[$i]['modulo_modelo'] =  $d['modulo_modelo']['mmdescricao'];
                }else{
                    $dados[$i]['modulo_modelo'] =  "sem modelo";
                }

                if($dados[$i]['proprietario']){
                    $dados[$i]['proprietario'] =  $d['proprietario']['clnome'];
                }
            }

            return Excel::create('modulos', function($excel) use ($dados){
                $excel->sheet('modulos', function($sheet) use ($dados)
                {
                    $sheet->fromArray($dados);
                    $sheet->row(1, array(
                        'Serial', 'Status', 'IMEI', 'Data de instalação', 'Modelo' , 'Chip/SIM', 'Proprietário'
                    ));
                });
            })->download($type);
        }
    }

    public function buscar(Request $request)
    {
        $busca = $request->mod;
        $modulos = Modulo::join('modulo_modelos', 'momodelo', 'mmcodigo')
            ->where('mmdescricao', 'ILIKE', '%'.trim($busca).'%')
            ->orWhere(function ($query) use ($busca){
                $query->whereRaw("mocodigo::varchar ILIKE '%$busca%'");
            })
            ->whereRaw("mocodigo not in (select mocodigo from modulos
                    join veiculos on mocodigo = vemodulo
                    where vestatus = 'A')")
            ->get();

        return response([
            'modulos' => $modulos,
        ]);
    }

    public function monitor(){
        dd("aqui");
    }
}

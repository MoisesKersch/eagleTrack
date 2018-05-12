<?php

namespace App\Http\Controllers\Cadastros;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\Pontos;
use App\Models\Email;
use Illuminate\Validation\Rule;
use App\Models\ModulosSistema;
use App\Models\PerfilItens;
use App\Models\Perfil;
use App\Models\PerfilPermissoes;
use Excel;
use DB;
use App\Helpers\KeyHelper;
use App\Http\Controllers\Cadastros\TelefonesController as Telefones;

class ClientesController extends Controller
{
    private $rules = [
        'clnome' => 'required',
        'cldocumento' => 'required|cpfcnpj|unique:clientes',
        'clcidade' => 'required',
        'clemail[]' => 'email',
    ];

    public function listar(Request $request)
    {
        $status = $request->status;
        $tipo = $request->tipo;
        if($tipo == "todos") {
            $clientes = Cliente::with('telefones', 'email', 'cidade', 'cidade.estado');
                if($status == 'ativo'){
                    $clientes->where('clstatus', '=', 'A');
                }elseif($status == 'inativo') {
                    $clientes->where('clstatus', '=', 'I');
                }else{
                    $clientes->whereNotNull('clstatus');
                }
        }else {
            $t = $tipo == 'fisica' ? 'F' : 'J';
            $clientes = Cliente::where('cltipo', '=', $t)
                ->with('telefones', 'email', 'cidade', 'cidade.estado');
            if($status == 'ativo'){
                $clientes->where('clstatus', '=', 'A');
            }elseif($status == 'inativo') {
                $clientes->where('clstatus', '=', 'I');
            }else{
                $clientes->whereNotNull('clstatus');
            }
        }
        $clientes = $clientes->get();

        return view('cadastros.clientes.listar', compact('clientes', 'tipo', 'status'));
    }
    public function criar()
    {
        $cidades = Cidade::all();
        $key = new KeyHelper;
        $key = $key->keyGenerate();
        $mdSistemas = ModulosSistema::all();

        return view('cadastros.clientes.cadastro', compact('cidades', 'key', 'mdSistemas'));
    }
    public function salvar(Request $request)
    {
        $dados = $request->all();
        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $cliente = new Cliente($dados);

        if($request->file('cllogo') && $request->file('cllogo')->isvalid()) {
            $destinationpath = public_path('images/logosclientes');
            $extencion = $request->file('cllogo')->getclientoriginalextension();
            $filename = str_random(12).'.'.$extencion;
            $request->file('cllogo')->move($destinationpath, $filename);
            $cliente->cllogo = 'images/logosclientes/'.$filename;
        }
        $cliente->clstatus = 'A';
        $cliente->clfantasia = $request->clfantasia ? : $request->clnome;
        $cliente->save();

        $telefone = new Telefones;
        if(!empty($request->clfone))
            $telefone->criar($cliente->clcodigo, $request->clfone);
        if(!empty($request->clemail)) {
            foreach($request->clemail as $mail){
                $email = new Email();
                $email->ememail = $mail;
                $email->emproprietario = $cliente->clcodigo;
                $email->save();
            }
        }

        $cliente->modulosSistema()->attach($request->mscliente);
        return redirect('painel/cadastros/clientes')->with('success', 'Cliente salvo!!!');
    }

    public function editar($id)
    {
        try {
            $cliente = Cliente::with('telefones', 'email', 'cidade', 'cidade.estado', 'pontosEspera', 'modulosSistema')->find($id);
            $cidades = Cidade::select('cicodigo', 'cinome')->get();
            $pontos = Pontos::where('pocodigocliente', '=', $cliente->clcodigo)
                ->get();

            $key = $cliente->clapikey;
            $espera = [];
            foreach ($cliente->pontosEspera as $esp) {
                $espera[] = $esp->pocodigo;
            }

            if(empty($key)) {
                $key = new KeyHelper;
                $key = $key->keyGenerate();
            }
            $mdSistemas = ModulosSistema::all();
            $modSisCliente = [];
            foreach($cliente->modulosSistema as $modSis){
                $modSisCliente[] = $modSis->mscodigo;
            }

            return view('cadastros.clientes.editar', compact('cliente', 'cidades', 'key', 'pontos', 'espera', 'mdSistemas', 'modSisCliente'));
        } catch (\Exception $e) {
            return back();
        }
    }

    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();
        $cliente = Cliente::find($id);

        $rules = [
            'clnome' => 'required',
            'cldocumento' => Rule::unique('clientes')->ignore($cliente->clcodigo, 'clcodigo'),
            'clcidade' => 'required',
            'clemail[]' => 'email',
        ];
        // if($request->cljornadamotoristasemajudante == 'T')
        //     $rules = array_merge($rules, ['phcliente' => 'required']);

        $validator = Validator::make($dados, $rules);
        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $logo = NULL;
        if($request->file('cllogo') && $request->file('cllogo')->isValid()) {
            if(!empty($cliente->cllogo) && file_exists(public_path($cliente->cllogo))){
                unlink(public_path($cliente->cllogo));
            }

            $destinationPath = public_path('images/logosClientes');
            $extencion = $request->file('cllogo')->getClientOriginalExtension();
            $fileName = str_random(12).'.'.$extencion;
            $request->file('cllogo')->move($destinationPath, $fileName);
            $logo = 'images/logosClientes/'.$fileName;
            $cliente->fill($dados);
            $cliente->cllogo = $logo;
        }else{
            $cliente->fill($dados);
        }
        $cliente->clfantasia = $request->clfantasia ? : $request->clnome;
        $cliente->save();

        $cliente->pontosEspera()->sync($request->phcliente);

        $telefone = new Telefones;
        $telefone->editar($cliente->clcodigo, $request->clfone);
        foreach($cliente->email as $old) {
            if(!in_array($old->ememail, $request->clemail)){
                $old->delete();
            }
        }

        foreach($request->clemail as $mail) {
            $email = Email::firstOrCreate(['ememail' => $mail, 'emproprietario' => $cliente->clcodigo]);
        }
        $cliente->modulosSistema()->sync($request->mscliente);

        PerfilPermissoes::join('perfis', 'ppperfilcodigo','pecodigo')
            ->join('perfil_itens', 'ppperfilitens', 'picodigo')
            ->where('pecliente', $cliente->clcodigo)
            ->whereNotIn('pimodulo_sistema', isset($request->mscliente)?$request->mscliente: [])->delete();

        return redirect('painel/cadastros/clientes')->with('success', 'Editado chid-classom sucesso!!!');
    }

    public function filtros()
    {
        $clientes = Cliente::with('email', 'telefones', 'cidade', 'cidade.estado')->get();
        return response ([
            'clientes' => $clientes,
            'nome' => \Auth::user()->name,
        ]);
    }
    public function emailExcluir(Request $request)
    {
        $email = Email::find($id);
        if($email) {
            $email->delete();
        }
        return response([
            'mensagem' => 'Deletado com sucesso!',
            'status' => '200',
        ]);
    }
    public function ativar(Request $request)
    {
        $id = $request->id;
        $cliente = Cliente::find($id);
        $cliente->clstatus = 'A';
        $cliente->save();

        return response ([
            'mensagem' => 'Cliente atializado',
            'status'  => '200'
        ]);
    }
    public function desativar($id)
    {
        $cliente = Cliente::find($id);
        return view('cadastros.clientes.desativar', compact('cliente'));
    }
    public function desable($id)
    {
        $cliente = Cliente::find($id);
        $cliente->clstatus = 'I';
        $cliente->save();
        return redirect('painel/cadastros/clientes')->with('success', 'Cliente desativado!!!');
    }

    public function excel(Request $request, $type)
    {
        $tipo = $request->tipo;
        $status = $request->status;
        if($tipo == 'todos'){
        $data = Cliente::select('clnome',
            DB::raw('(select cinome from cidades where cicodigo = clcidade limit 1) as cidade'),
            'cllogradouro',
            DB::raw('(select tlnumero from telefones where tlproprietario = clcodigo limit 1) as telefone'),
            DB::raw('(select ememail from emails where emproprietario = clcodigo limit 1) as email'));
            if($status == 'ativo'){
                    $data->where('clstatus', '=', 'A');
                }elseif($status == 'inativo') {
                    $data->where('clstatus', '=', 'I');
                }else{
                    $data->whereNotNull('clstatus');
                }
        }else {
            $t = $tipo == 'fisica' ? 'F' : 'J';
            $data = Cliente::select('clnome',
            DB::raw('(select cinome from cidades where cicodigo = clcidade limit 1) as cidade'),
            'cllogradouro',
            DB::raw('(select tlnumero from telefones where tlproprietario = clcodigo limit 1) as telefone'),
            DB::raw('(select ememail from emails where emproprietario = clcodigo limit 1) as email'))
            ->where('cltipo', '=', $t);
            if($status == 'ativo'){
                    $data->where('clstatus', '=', 'A');
                }elseif($status == 'inativo') {
                    $data->where('clstatus', '=', 'I');
                }else{
                    $data->whereNotNull('clstatus');
                }
        }
        $data = $data->get();
        return Excel::create('Listagem_clientes', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
				$sheet->fromArray($data);
                $sheet->row(1, array(
                    'Nome', 'Cidade', 'Logradouro', 'Telefone', 'ememail'
                ));
	        });
		})->download($type);
    }
    public function pdf(Request $request)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $dia = new \Datetime();

        $tipo = $request->tipo;
        $status = $request->status;
        if($tipo == 'todos'){
        $data = Cliente::select('clnome',
            DB::raw('(select cinome from cidades where cicodigo = clcidade limit 1) as cidade'),
            'cllogradouro',
            DB::raw('(select tlnumero from telefones where tlproprietario = clcodigo limit 1) as telefone'),
            DB::raw('(select ememail from emails where emproprietario = clcodigo limit 1) as email'), 'cltipo');
            if($status == 'ativo'){
                    $data->where('clstatus', '=', 'A');
                }elseif($status == 'inativo') {
                    $data->where('clstatus', '=', 'I');
                }else{
                    $data->whereNotNull('clstatus');
                }
        }else {
            $t = $tipo == 'fisica' ? 'F' : 'J';
            $data = Cliente::select('clnome',
            DB::raw('(select cinome from cidades where cicodigo = clcidade limit 1) as cidade'),
            'cllogradouro',
            DB::raw('(select tlnumero from telefones where tlproprietario = clcodigo limit 1) as telefone'),
            DB::raw('(select ememail from emails where emproprietario = clcodigo limit 1) as email'))
            ->where('cltipo', '=', $t);
            if($status == 'ativo'){
                    $data->where('clstatus', '=', 'A');
                }elseif($status == 'inativo') {
                    $data->where('clstatus', '=', 'I');
                }else{
                    $data->whereNotNull('clstatus');
                }
        }
        $data = $data->get();

        $pdf = \PDF::loadView('cadastros.clientes.pdf', compact('data', 'dia'));
        return $pdf->stream();
    }

    public function tipo(Request $request)
    {
        $clientes = Cliente::all();
        return response([
            'clientes' => $clientes,
        ]);
    }
    public function key()
    {
        $key = new KeyHelper;
        $key = $key->keyGenerate();
        return response([
            'key' => $key,
        ]);
    }

    public function keyRemove($id)
    {
        $cliente = Cliente::find($id);
        $cliente->clapikey = '';
        $cliente->save();

        return response ([
            'mensagem' => 'Salvo com sucesso!',
            'status'  => '200'
        ]);
    }
}

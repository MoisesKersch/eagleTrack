<?php

namespace App\Http\Controllers\Cadastros;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Bilhete;
use App\Models\Modulo;
use App\Models\VeiculoRegiao;
use App\Models\ComandosFila;
use App\User;
use App\Models\Regioes;
use DB;
use Validator;
use App\Helpers\PdfHelper;
use App\Services\MotoristaAjudanteService;
use Excel;
use App\Helpers\CheckIdHelper;

class VeiculosController extends Controller
{
    private $rules = [
        'vedescricao'    => 'required',
        'veprefixo'      => 'required|Max:8',
        'veplaca'        => 'required|unique:veiculos,veplaca',
        'veproprietario' => 'required',
        'vetipo'         => 'required'
    ];



    public function listar(Request $request, $status = '', $comModulo = '')
    {

        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
            $adm = true;
        }else{
            $clientes = \Auth::user()->clientes;
            $adm = false;
        }

        return view('cadastros.veiculos.listar', compact('clientes', 'adm'));
    }

    public function buscar(Request $request, $status = '', $comModulo = '',$clientesbusca = '')
    {
        $status = $request->status;
        $comModulo = $request->modulo;
        $clientesbusca = $request->clientesbusca;

        // dd(\Auth::user()->id);
        if($comModulo == 'todos'){
            if(\Auth::user()->usumaster == 'S') {
                $veiculos = Veiculo::with('cliente')
                    ->leftJoin('modulos', 'vemodulo', '=', 'mocodigo');
                $clientes = Cliente::select('clcodigo', 'clnome')->get();
            }else {
            $veiculos = Veiculo::with('cliente')
                ->join('clientes', 'clcodigo', '=', 'veproprietario')
                ->leftJoin('modulos', 'vemodulo', '=', 'mocodigo')
                ->whereIn('veproprietario', $clientesbusca);
                // ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
                $clientes = \Auth::user()->clientes;
            }
            if($status == 'ativo'){
                $veiculos->where('vestatus', '=', 'A');
            }elseif($status == 'inativo'){
                $veiculos->where('vestatus', '=', 'I');
            }
        }elseif($comModulo == 'sim'){
            if(\Auth::user()->usumaster == 'S') {
                $veiculos = Veiculo::with('cliente')->whereNotNull('vemodulo')
                    ->leftJoin('modulos', 'vemodulo', '=', 'mocodigo');
            }else{
                $veiculos = Veiculo::with('cliente')->whereNotNull('vemodulo')
                    ->leftJoin('modulos', 'vemodulo', '=', 'mocodigo')
                    ->join('clientes', 'clcodigo', '=', 'veproprietario')
                    ->whereIn('veproprietario', $clientesbusca);
                    // ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
                    // ->where('uclusuario', '=', \Auth::user()->id);
            }
            if($status == 'ativo'){
                $veiculos->where('vestatus', '=', 'A');
            }elseif($status == 'inativo'){
                $veiculos->where('vestatus', '=', 'I');
            }
        }else{
            if(\Auth::user()->usumaster == 'S') {
                $veiculos = Veiculo::with('cliente')->whereNull('vemodulo');
            }else {
                $veiculos = Veiculo::with('cliente')->whereNull('vemodulo')
                    ->join('clientes', 'clcodigo', '=', 'veproprietario')
                    ->whereIn('veproprietario', $clientesbusca);
                    // ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
                    // ->where('uclusuario', '=', \Auth::user()->id);
            }
            if($status == 'ativo'){
                $veiculos->where('vestatus', '=', 'A');
            }elseif($status == 'inativo'){
                $veiculos->where('vestatus', '=', 'I');
            }
        }

        if(\Auth::user()->usumaster == 'S') {
            $veiculos->whereIn('veproprietario', $clientesbusca);
        }else{
            $veiculos->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : []);
        }

        $veiculos = $veiculos->get();

        foreach ($veiculos as $key => $veiculo) {
            $veiculos[$key]->veproprietario = $veiculos[$key]->cliente->clnome;
        }

        return response([
            'veiculos' => $veiculos
        ]);
    }

    public function criar()
    {
        $ids_reg="0,0";
        $modulos = null;
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::all();
            $modulos = Modulo::where('mostatus','A')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        return view('cadastros.veiculos.criar', compact('clientes','modulos','ids_reg'));
    }


    public function salvar(Request $request)
    {
        $dados = $request->all();
        $dados['veplaca'] = strtoupper($request->veplaca);
        $validator = Validator::make($dados, $this->rules);
        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //Sem módulo
        if($dados['vemodulo'] <= -1){
            $dados['vemodulo'] = null;
        }

        $veiculo = new Veiculo($dados);
        $veiculo->vecusto = (float) str_replace(',','.',$request->vecusto);
        $veiculo->vemaxpeso = (float) str_replace(',','',$request->vemaxpeso);
        $veiculo->vecubagem = (float) str_replace(',','',$request->vecubagem);
        $veiculo->veautonomia = (float) str_replace(',','',$request->veautonomia);
        $veiculo->veplaca = strtoupper($request->veplaca);
        $veiculo->vemaxentregas = (int) $request->vemaxentregas;
        $veiculo->vestatus = 'A';
        try{
            $veiculo->save();
            if(isset($dados['veregioes'])){
                foreach ($dados['veregioes'] as $key => $regiao) {
                    $r = new VeiculoRegiao();
                    $r->vrveiculo = $veiculo->vecodigo;
                    $r->vrregiao = $regiao;
                    $r->save();
                }
            }
            // criar os registos de veic_regiao;

        }catch(Exception $e){
           // do task when error
           echo $e->getMessage();
        }

        if(isset($veiculo->modulo)){
            $modulo = Modulo::find($veiculo->modulo->mocodigo);
            if(intval($dados['vehodometroatual']) != null && intval($dados['vehodometroatual']) != intval($dados['orig_vehodometroatual'])){
                $modulo->mohodometro = intval($dados['vehodometroatual'])*1000;
            }
            if(intval($dados['vehorimetroatual']) != null && intval($dados['vehorimetroatual']) != intval($dados['orig_vehorimetroatual'])){
                $modulo->mohorimetro = intval($dados['vehorimetroatual']);
            }
            $modulo->save();
        }

        return redirect('painel/cadastros/veiculos')->with('success', 'Veiculo salvo!!!');
    }

    public function editar($id)
    {
        if(!CheckIdHelper::checkId('veiculos', 'vecodigo', 'veproprietario', $id)) return redirect()->back();

        $modulos = null;
        $ids_reg="";
        $cont = 0;

        $veiculo = Veiculo::with('modulo','cliente','regioes')->find($id);

        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::all();
            $modulos = Modulo::where('mostatus','A')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        foreach ($veiculo->regioes as $key => $regiao){
            if($cont == 0){
                $ids_reg = $ids_reg.$regiao->recodigo;
            }else{
                $ids_reg = $ids_reg.','.$regiao->recodigo;
            }
            $cont++;
        }

        return view('cadastros.veiculos.editar', compact('veiculo','modulos','clientes','ids_reg'));
    }

    public function VerificaModuloUsado(Request $request)
    {

        $veiculo = Veiculo::where('vemodulo',$request->mocodigo)->first();
        $modulo = Modulo::find($request->mocodigo);

        if(count($veiculo) > 0){
            $veiculo->veproprietario = $veiculo->cliente->clnome;
            $return = $veiculo;
        }else{
            $return = 0;
        }

        return response([
            'veiculo' => $return,
            'modulo' => $modulo
        ]);
    }

    public function DesvincularModuloUsado(Request $request)
    {
        $veiculo = Veiculo::find($request->vecodigo);
        $veiculo->vemodulo = null;
        $veiculo->save();
    }


    public function atualizar(Request $request, $id)
    {
        $dados = $request->all();
        $dados['veplaca'] = strtoupper($request->veplaca);
        $validator = Validator::make($dados,
             [
                'vedescricao'    => 'required',
                'veplaca'        => 'required|unique:veiculos,veplaca,'.$id.',vecodigo',
                'veprefixo'      => 'required|Max:8',
                'veproprietario' => 'required',
                'vetipo'         => 'required',
            ]);

        if($validator->fails()){
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //Sem módulo
        if($dados['vemodulo'] <= -1){
            $dados['vemodulo'] = null;
        }

        //TODO apagar relaçõe deste veículo com regiao e criar novamente.
        VeiculoRegiao::where('vrveiculo',$id)->delete();

        $veiculo = Veiculo::find($id);
        $veiculo->fill($dados);
        $veiculo->vecusto = (float) str_replace(',','.',$request->vecusto);
        $veiculo->veplaca = strtoupper($request->veplaca);
        $veiculo->vetipo = $request->vetipo;
        $veiculo->vemaxentregas = (int) $request->vemaxentregas;
        // $veiculo->vemaxpeso = $request->vemaxpeso;
        // $veiculo->vecubagem = $request->vecubagem;
        $veiculo->vehorainiciotrabalho = $request->vehorainiciotrabalho;
        $veiculo->vehorafinaltrabalho = $request->vehorafinaltrabalho;
        $veiculo->vemaxpeso = (float) str_replace(',','',$request->vemaxpeso);
        $veiculo->vecubagem = (float) str_replace(',','',$request->vecubagem);
        $veiculo->veautonomia = (float) str_replace(',','',$request->veautonomia);


        try{
            $veiculo->save();
            if(isset($dados['veregioes'])){
                foreach ($dados['veregioes'] as $key => $regiao) {
                    $r = VeiculoRegiao::firstOrCreate(array('vrveiculo' => $veiculo->vecodigo, 'vrregiao' => $regiao));
                    $r->save();
                }
            }
            // criar os registos de veic_regiao;

        }catch(Exception $e){
           // do task when error
           echo $e->getMessage();
        }

        if(isset($veiculo->modulo)){
            $modulo = Modulo::find($veiculo->modulo->mocodigo);
            if(intval($dados['vehodometroatual']) != null && intval($dados['vehodometroatual']) != intval($dados['orig_vehodometroatual'])){
                $modulo->mohodometro = intval($dados['vehodometroatual'])*1000;
            }
            if(intval($dados['vehorimetroatual']) != null && intval($dados['vehorimetroatual']) != intval($dados['orig_vehorimetroatual'])){
                $modulo->mohorimetro = intval($dados['vehorimetroatual']);
            }
            $modulo->save();
        }

        //TODO NÃO APAGAR!!!!!! vais ser usado posteriormente
        // if($dados['vehodometroatual'] != null && $dados['vehodometroatual'] != $dados['orig_vehodometroatual']){
        //     $comandoFila = new ComandosFila();
        //     $comandoFila->cfmodelorastreador = $veiculo->modulo->momodelo;
        //     $comandoFila->cfparametro = $dados['vehodometroatual'] * 1000;//converte para quilometros
        //     $comandoFila->cfmodulo = $veiculo->modulo->mocodigo;
        //     $comandoFila->cfcomando = 'hodometro';
        //     $comandoFila->cfstatus = 'P';
        //     $comandoFila->save();
        // }
        // if($dados['vehorimetroatual'] != null && $dados['vehorimetroatual'] != $dados['orig_vehorimetroatual']){
        //     $comandoFila = new ComandosFila();
        //     $comandoFila->cfmodelorastreador = $veiculo->modulo->momodelo;
        //     $comandoFila->cfparametro = $dados['vehorimetroatual'];
        //     $comandoFila->cfmodulo = $veiculo->modulo->mocodigo;
        //     $comandoFila->cfcomando = 'horimetro';
        //     $comandoFila->cfstatus = 'P';
        //     $comandoFila->save();
        // }

        return redirect('painel/cadastros/veiculos')->with('success', 'Veiculo editado!!!');
    }

    public function alterarStatus(Request $request)
    {
        $vecodigo = $request->vecodigo;
        if(!CheckIdHelper::checkId('veiculos', 'vecodigo', 'veproprietario', $vecodigo)) return redirect()->back();

        $veiculo = Veiculo::find($vecodigo);

        if($veiculo->vestatus == "A") {
            $veiculo->vestatus = "I";
            $status = "I";
        } else{
            $veiculo->vestatus = "A";
            $status = "A";
        }
        $veiculo->save();

        return $status;
    }

    public function desativar($id)
    {
        if(!CheckIdHelper::checkId('veiculos', 'vecodigo', 'veproprietario', $vecodigo)) return redirect()->back();

        $veiculo = Veiculo::find($id);
        return view('cadastros.veiculos.desativar', compact('veiculo'));
    }

    function atualizarMotorista(Request $request){

        $mas = new MotoristaAjudanteService();

        //desassociar como motorista
        $mas->desassociarMA($request->motoristaId, true);
        $mas->desassociarMA($request->ajudanteId, true);

        //desassociar como ajudante
        $mas->desassociarMA($request->motoristaId, false);
        $mas->desassociarMA($request->ajudanteId, false);

        try {
            $veiculo = Veiculo::find($request->veiculoId);
            $veiculo->vemotorista = $request->motoristaId;
            $veiculo->veajudante = $request->ajudanteId;
            $veiculo->save();

            //Atualizar do ultimo bilhete e ultima ignicao do veículo com 9 pra posterior com o motorista e ajudante
            if(!isset($request->ajudanteId)){
                $request->ajudanteId = 0;
            }
            if(!isset($request->motoristaId)){
                $request->motoristaId = 0;
            }
            $mas->setBiajudante($request->ajudanteId, $veiculo, 9);
            $mas->setIvajudante($request->ajudanteId, $veiculo, 9);

            $mas->setBimotorista($request->motoristaId, $veiculo, 9);
            $mas->setIvmotorista($request->motoristaId, $veiculo, 9);

            return ['ok' => 'Motorista atualizado!'];
        }catch (\Exception $e) {
            return $e->getMessage();
            return ['erro' => 'erro'];
        }
    }

    public function cliente(Request $request)
     {
         $id = $request->id;
         if(empty($id)){
             return response ([
                 'cliente' => ''
             ]);
         }
         $veiculos = Cliente::select(\DB::raw("veplaca, veprefixo, coalesce(vedescricao, '') as vedescricao, clnome, vestatus"))
             ->join('veiculos', 'veproprietario', '=', 'clcodigo')
             ->whereIn('clcodigo', $id)->get();

         return response ([
             'veiculos' => $veiculos,
         ]);
     }

     public function veiculo(Request $request)
     {
         $id = $request->id;

         if(empty($id)) {
             return response ([
                 'dados' => '',
             ]);
         }
         $dados = Cliente::select('vecodigo', 'veplaca','veprefixo')
             ->join('veiculos', 'veproprietario', '=', 'clcodigo')
             ->where('vestatus','=','A')
             ->whereIn('clcodigo', $id)->get();

         return response ([
             'dados' => $dados,
         ]);
     }

     public function last_bilhete(Request $request){

         $dado = Bilhete::select('bihodometro')->where('biplaca',$request->placa)->orderBy('bidataevento','DESC')->first();

         return response([
             'dado' => $dado
         ]);
     }

     public function regioesCliente(Request $request){
         $regioes = Regioes::regioesCliente($request->cliente);

         return response ([
             'regioes' => $regioes,
         ]);
     }

     public function getHodometroHorimetro(Request $request){
        $dados = Veiculo::select(DB::raw('coalesce(mohodometro, 0) as mohodometro, coalesce(mohorimetro, 0) as mohorimetro'))
             ->Leftjoin('modulos', 'vemodulo', '=', 'mocodigo')
             ->whereIn('veplaca', [$request->placa])
             ->get();
        return response([
            'mohodometro' => $dados[0]->mohodometro,
            'mohorimetro' => $dados[0]->mohorimetro
        ]);
     }
}

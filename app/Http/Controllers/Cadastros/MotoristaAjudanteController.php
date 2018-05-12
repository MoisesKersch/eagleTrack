<?php

namespace App\Http\Controllers\Cadastros;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Cliente;
use App\Models\Motorista;
use App\Models\MotoristaPonto;
use App\Models\Pontos;
use App\Models\Veiculo;
use App\Models\GrupoMotorista;
use App\Models\JornadaTrabalho;
use App\Models\Licenca;
use App\Models\Email;
use Illuminate\Validation\Rule;
use App\Helpers\PdfHelper;
use App\Helpers\CheckIdHelper;
use Excel;
use Auth;
use DB;
use App\Http\Controllers\Cadastros\TelefonesController as Telefones;

class MotoristaAjudanteController extends Controller
{
    private $rules = [
        'mtnome' => 'required',
        'mtcpf' => 'nullable|cpf|unique:motoristas,mtcpf',
        'mtcliente' => 'required',
        'mtperfil' => 'required'
    ];

    public function index(Request $request)
    {
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
            $adm = true;
        }else{
            $clientes = \Auth::user()->clientes;
            $adm = false;
        }

        return view('cadastros.motoristas_ajudantes.index')
            ->with('clientes', $clientes)
            ->with('adm', $adm);
    }


    public function listar(Request $request){

        $motoristas = $this->buscarMotoristas($request);

        foreach ($motoristas as $key => $motorista) {
            $motoristas[$key]->cliente = $motoristas[$key]->cliente->clnome;
        }

        return response([
            'motoristas' => $motoristas
        ]);

    }

    public function buscarMotoristas(Request $request){
        $usuario = Auth::user();
        $status = $request->status;
        $flg_ma = $request->flg_ma;
        $clientesbusca = $request->clientesbusca;
        $motoristas = Motorista::select('*')
                        ->addSelect(DB::raw(" to_char (mtcnhvalidade, 'dd/mm/yyyy') as mtcnhvalidade "))
                        ->whereNotNull('mtcliente');

        if($status  != null){
            if($status == 'ativo'){
                $motoristas = $motoristas->where('mtstatus', '=', 'A');
            }elseif ($status == 'inativo') {
                $motoristas = $motoristas->where('mtstatus', '=', 'I');
            }
        }
        if($flg_ma  != null){
            if($flg_ma == 'motoristas'){
                $motoristas = $motoristas->where('mtperfil', 'like', '%M%');
            }elseif ($flg_ma == 'ajudantes') {
                $motoristas = $motoristas->where('mtperfil', 'like', '%A%');
            }
        }

        $motoristas->whereIn('mtcliente', $clientesbusca);

        $motoristas = $motoristas->orderBy('mtnome', 'ASC');
        $motoristas = $motoristas->get();

        return $motoristas;

    }


public function save(Request $request)
    {

      $dados = $request->all();
      if(isset($dados['mtperfila']) && isset($dados['mtperfilm'])){
         $dados['mtperfil'] = "MA";
      }elseif(isset($dados['mtperfila']) && !isset($dados['mtperfilm'])){
         $dados['mtperfil'] = "A";
      }elseif(isset($dados['mtperfilm']) && !isset($dados['mtperfila'])){
         $dados['mtperfil'] = "M";
      }

      if($dados["mtcodigo"] == null){
          $validator = Validator::make($dados, $this->rules);
      }else{
          $validator = Validator::make($dados, [
              'mtnome' => 'required',
              'mtcpf' => 'nullable|cpf|unique:motoristas,mtcpf,'.$dados["mtcodigo"].',mtcodigo',
              'mtcliente' => 'required',
              'mtperfil' => 'required',
          ]);
      }
      if($validator->fails()) {
          return redirect()->back()
              ->withErrors($validator)
              ->withInput($dados);
      }else{
        if($dados["mtcodigo"] == null){
          $motorista = new Motorista();
        }else{
          $motorista = Motorista::find($dados["mtcodigo"]);
        }
        $motorista->mtcliente = $dados["mtcliente"];
        $motorista->mtstatus = $dados["status"];
        $motorista->mtnome = $dados["mtnome"];
        $motorista->mtcpf = $dados["mtcpf"];
        $motorista->mtrg = $dados["mtrg"];
        $motorista->mttelefone = $dados["mttelefone"];
        $motorista->mtdatanasc = $dados["mtdatanasc"];
        $motorista->mtcnhnumero = $dados["mtcnhnumero"];
        $motorista->mtgrupo = ($dados["mtgrupo"] == 'null'? null : $dados["mtgrupo"]);
        $motorista->mtcnhvalidade = $dados["mtcnhvalidade"];
        $motorista->mtcracha = $dados["mtcracha"];
        $motorista->mtperfil = $dados["mtperfil"];
        $motorista->mtjornada = $dados['mtjornada'];

        if(isset($dados["mtcnh"])){
          $motorista->mtcnh = $dados["mtcnh"];
        }
        if($dados["mtendereco"] != null){
          $motorista->mtendereco = $dados["mtendereco"];
        }
        $motorista->mtlongitude = $dados["mtlongitude"];
        $motorista->mtlatitude = $dados["mtlatitude"];
        $motorista->mtraio = $dados["mtraio"];
        $motorista->save();

        MotoristaPonto::where('mpmotorista', '=', $motorista->mtcodigo)->delete();

        if(isset($dados["pontosRelacionados"])){
          foreach ($dados["pontosRelacionados"] as $value) {
            $mp = new MotoristaPonto();
            $mp->mpponto = $value;
            $mp->mpmotorista = $motorista->mtcodigo;
            $mp->save();
          }
        }

        if(!empty($request->mllicenca)){
            foreach ($request->mllicenca as $i => $mllicenca) {
                $addLicenca[] = ['mllicenca' => $mllicenca, 'mlvalidade' => $request->mlvalidade[$i]];
            }

            $motorista->licencas()->attach($addLicenca);
        }

        return redirect('/painel/cadastros/motoristas')->with('success', 'Motorista salvo!!!');
      }

    }

    public function alterarStatus(Request $request)
    {
        $mtcodigo = $request->mtcodigo;
        if(!CheckIdHelper::checkId('motoristas', 'mtcodigo', 'mtcliente', $mtcodigo)) return redirect()->back();

        $motorista = Motorista::find($mtcodigo);

        if($motorista->mtstatus == "A") {
            $motorista->mtstatus = "I";
            $status = "I";
        } else{
            $motorista->mtstatus = "A";
            $status = "A";
        }
        $motorista->save();

        return $status;
    }

    public function show($id)
    {
        if(!CheckIdHelper::checkId('motoristas', 'mtcodigo', 'mtcliente', $id)) return redirect()->back();

        $motorista = Motorista::with('pontos', 'grupo', 'licencas')
                        ->select('*')
                        ->addSelect(DB::raw(" to_char (mtcnhvalidade, 'dd/mm/yyyy') as mtcnhvalidade "))
                        ->find($id);

        $gmotoristas = GrupoMotorista::where('gmcliente', '=', $motorista->mtcliente)
                            ->where('gmstatus','A')->get();
        $pontos = Pontos::select('pocodigo', 'podescricao')
            ->where('pocodigocliente', '=', $motorista->mtcliente)->get();

        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::all();
        }else{
            $clientes = \Auth::user()->clientes;
        }

        foreach($motorista->pontos as $pts){
            $pontosMt[] = $pts->pocodigo;
        }
        $jornadas = JornadaTrabalho::select('jtcodigo', 'jtdescricao')
            ->where('jtcliente', '=', $motorista->mtcliente)
        ->get();
        return view('cadastros.motoristas_ajudantes.cadastro', compact('gmotoristas', 'clientes','pontos', 'pontosMt', 'motorista', 'jornadas'));
    }

    public function cadastro()
    {
        if(\Auth::user()->usumaster == 'S') {
            $gmotoristas = GrupoMotorista::where('gmstatus','A')->get();
            $clientes = Cliente::all();
            // $pontos = Pontos::all();
        }else{
            $gmotoristas = GrupoMotorista::join('clientes', 'clcodigo', '=', 'gmcliente')
                ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
                ->where('gmstatus', 'A')
                ->where('uclusuario', '=', \Auth::user()->id)->get();
            $clientes = \Auth::user()->clientes;
            // $pontos = Pontos::where('pocodigocliente', '=', \Auth::user()->id)->get();
        }

        return view('cadastros.motoristas_ajudantes.cadastro', compact('gmotoristas', 'clientes'));
    }


    public function buscarPontosCliente()
    {
      $usuario = Auth::user();
      $clcodigo = $usuario->usucliente;

      $pontosCliente = Pontos::select('pocodigo')->addSelect('podescricao')
                  ->leftJoin('clientes', 'clientes.clcodigo', '=', 'pocodigocliente')
                  ->where('pocodigocliente', '=', $clcodigo)->get();

     echo json_encode($pontosCliente);
    }

    public function dadosCadastro(Request $request)
    {
        $id = $request->id;
        $mtcodigo = $request->mtcodigo;
        $pontosMotorista = null;

        $grupos = GrupoMotorista::where('gmcliente', '=', $id)
                            ->where('gmstatus','A')->get();

        $pontos = Pontos::select('pocodigo', 'podescricao')
            ->where('pocodigocliente', '=', $id)->get();

        $jornadas = JornadaTrabalho::where('jtcliente', '=', $id)->get();

        if(isset($mtcodigo) && $mtcodigo != null){
            $pontosMotorista = MotoristaPonto::select('mpcodigo', 'mpponto','mpmotorista')
                ->where('mpmotorista', '=', $mtcodigo)->get();

        }
        $licencas = Licenca::where('licliente', '=', $id)->get();

        return response ([
            'grupos' => $grupos,
            'pontos' => $pontos,
            'pontosMotorista' => $pontosMotorista,
            'jornadas' => $jornadas,
            'licencas' => $licencas,
        ]);
    }


    public function cliente(Request $request)
    {
        $id = $request->id;
        if(empty($id)){
            return response ([
                'cliente' => ''
            ]);
        }
        $cliente = Cliente::select(DB::raw("mtnome, coalesce(mttelefone, '') as mttelefone,
            mtstatus, mtcodigo, coalesce(mtcracha, '') as mtcracha, coalesce(mtcnh, '') as mtcnh,
            coalesce(mtcnhvalidade, '') as mtcnhvalidade, clnome"))
            ->join('motoristas', 'mtcliente', '=', 'clcodigo')
            ->whereIn('clcodigo', $id)->get();

        return response ([
            'cliente' => $cliente,
        ]);
    }

    function listarMotoristas(Request $request)
    {
        $cliente = $request->cliente;
        $arrayMotoristas = [];

        $motoristas = Motorista::select(DB::raw('mtcodigo, mtnome, mtcliente, mtperfil'))
                            ->where('mtstatus', '=', 'A')
                            ->whereIn('mtcliente', $cliente)
                            ->get();

        foreach ($motoristas as $m) {
            $arrayMotoristas[$m['mtcliente']][] = $m;
        }

        return json_encode($arrayMotoristas);
    }

    function checkDisponibilidadeMA(Request $request)
    {
        $id = $request->cod;

        $veiculo = Veiculo::select('vecodigo','veprefixo','vemotorista','veajudante')
            ->where('vemotorista',$id)
            ->orWhere('veajudante',$id)
            ->first();

        return response([
            'veiculo' => $veiculo,
            'cod' => $id
        ]);
    }

    public function maisLicenca(Request $request)
    {
        $licenca = "";
        if(!empty($request->lidescricao)){
            $li = Licenca::where('lidescricao', $request->lidescricao)->get();
            if($li->isEmpty()) {
                $licenca = Licenca::create($request->all());
            }

            return response([
                'licenca' => $licenca,
                'mensagem' => 'Salvo com sucesso!',
                'codigo' => '200'
            ]);
        }
    }

    public function desassociarLicenca(Request $request)
    {
        $motorista = Motorista::find($request->mot);
        // $motorista->licencas()->attach($addLicenca);
        $motorista->licencas()->detach($request->id);
        return response([
            'mensagem' => 'Alterado com sucesso!',
            'codigo' => '200'
        ]);
    }
}

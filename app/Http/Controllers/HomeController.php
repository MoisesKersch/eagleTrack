<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\ManutencaoProgramada;
use App\Models\Cliente;
use App\Models\Regioes;
use App\Models\PerfilPermissoes;
use App\Models\JornadaTrabalho;
use App\Models\Motorista;
use App\Models\Perfil;
use App\User;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if(\Auth::user()->usumaster == 'S'){
            $clientes = Cliente::select('clcodigo', 'clnome', 'clfantasia')
                ->where('clstatus', 'A')->get();
            $veiculos = Veiculo::select('vecodigo', 'veplaca')
                ->where('vestatus', '=', 'A')->get();
        }else{
            //o where para trazer comente ativos esta na model no belongsToMany;
            $clientes = \Auth::user()->clientes()->get();
            $veiculos = Veiculo::select('vecodigo', 'veplaca')
                ->join('usuarios_clientes', 'uclcliente', '=', 'veproprietario')
                ->where('vestatus', '=', 'A')
                ->where('uclusuario', '=', \Auth::user()->usucliente)
                ->get();
        }

        $perfis = Perfil::join('perfil_permissoes', 'pecodigo', '=', 'ppperfilcodigo')
            ->join('users', 'usuperfil', '=', 'pecodigo')
            ->join('perfil_itens', 'ppperfilitens', '=', 'picodigo')
            ->where('id', '=', \Auth::user()->id)
            ->get();

        return view('home', compact('clientes', 'veiculos', 'perfis'));
    }

    public function painel(){

            $jt = JornadaTrabalho::find(141);
            $jt->getTotalHorasMensais();


        $veiculo = new Veiculo;
        $countVeiculos = $veiculo->totalVeiculos();
        $countClientes = Cliente::where('clstatus','=','A')->count();
        $countUsers = User::where('usuativo','=','S')->count();
        return view('painel',compact('countClientes','countVeiculos','countUsers'));
    }
    public function rankingKms(){
        $veiculo = new Veiculo;
        $rankingKms = $veiculo->rankingKms(5);
        return response(json_encode($rankingKms));
    }
    /*
    *   Funcao para buscar dados da previsao do tempo
    *
    */
    public function previsaoTempo(){
        $usuario = \Auth::user();
        $chave = '29adf99d';
        $ip = $_SERVER["REMOTE_ADDR"];
        $latlon = $usuario->cliente()->select('cllatitude','cllongitude')->get();
        $lat;
        $lon;
        if(!$latlon){
            $lat = "-27.1022145";
            $lon = "-52.6287346";
        }else{
            $lat = $latlon[0]->cllatitude;
            $lon = $latlon[0]->cllongitude;
        }
        // Obtem os dados da API passando os parametros
        $dados = $this->hg_request(array('lat' => $lat, 'lon' => $lon, 'user_ip' => $ip), $chave);
        // Formata dos dados recebidos
        return response(array(
            'temperatura' => $dados->results->temp.' ºC',
            'umidade' => $dados->results->humidity.' %',
            'descricao' => $dados->results->description,
            'cidade' => $dados->results->city,
            'nascer_do_sol' => $dados->results->sunrise,
            'por_do_sol' => $dados->results->sunset,
            'vento' => $dados->results->wind_speedy,
            'imagem' => 'imagens/'.$dados->results->img_id.'.png',
        ));

    }

    public function hg_request($parametros, $chave = null, $endpoint = 'weather'){
        $url = 'https://api.hgbrasil.com/'.$endpoint.'/?format=json&';
        if(is_array($parametros)){
        // Insere a chave nos parametros
            if(!empty($chave)) $parametros = array_merge($parametros, array('key' => $chave));
                // Transforma os parametros em URL
                foreach($parametros as $key => $value){
                if(empty($value)) continue;
                $url .= $key.'='.urlencode($value).'&';
            }
                // Obtem os dados da API
                $resposta = file_get_contents(substr($url, 0, -1));
                return json_decode($resposta);
            } else {
                return false;
            }
    }

    //Busca o alertas de manutenção
    public function alertasManutencao(){
        $usuario = \Auth::user();

        $km_ate_manutencao = " ((coalesce(mapkmprogramado, 0)) - (coalesce(bihodometro, 0)/1000)) ";
        $manutencoes = ManutencaoProgramada::select('macodigo', 'clnome' , 'ticodigo','vecodigo','mapstatus','mapcliente','biplaca','veprefixo',
                'vedescricao','timdescricao','timkmpadrao', DB::raw('(bihodometro / 1000) as bihodometro'),
                'mapkmprogramado','mapstatus',
                DB::raw(" $km_ate_manutencao as km_ate_manutencao"))
            ->join('tipo_manutencoes','ticodigo','=','maptipomanutencao')
            ->join('clientes','timproprietario','=','clcodigo')
            ->join('veiculos as v','vecodigo','=','mapcodigoveiculo')
            ->join('bilhetes','biplaca','=','veplaca');
            if($usuario->usumaster == "N"){
                $manutencoes = $manutencoes->whereIn('mapcliente', $usuario->getEmpresasUsuario());
            }
            $manutencoes = $manutencoes->where('bidataevento', '=', DB::raw('(select max(bidataevento) from bilhetes where biplaca = v.veplaca)'))
            ->where('mapstatus','=','P')
            ->orderBy('km_ate_manutencao','ASC')
            ->limit(5)->get();

        return response([
            'manutencoes' => $manutencoes
        ]);
    }

    //Busca o alertas de cnh's
    public function alertasCnhVencida(){
        $usuario = \Auth::user();

        $motoristas = Motorista::select('mtnome','mtcnhvalidade');
        if($usuario->usumaster == "N"){
            $motoristas = $motoristas->whereIn('mtcliente', $usuario->getEmpresasUsuario());
        }
        $motoristas = $motoristas->whereNotNull('mtcnhvalidade')
        ->orderBy('mtcnhvalidade','ASC')
        ->limit(5)->get();

        return response([
            'motoristas' => $motoristas
        ]);
    }

    //Verifica se a sessao será encerrada - Sessao se encerra em 10 horas
    public function verificaSessao(Request $request)
    {
        $sessaoReiniciada = session('sessao_reiniciada');
        //32400 - 9:00 h
        $tempoLimite = (int)strtotime('+35700 seconds', $sessaoReiniciada); // 9:55 horas
        $tempoAgora = strtotime('+900 seconds'); // 15 minutos

        if ($sessaoReiniciada == null || $tempoAgora >= $tempoLimite)
            return json_encode(true);

        return json_encode(false);
    }

    public function veregiao()
    {
        $usuario = \Auth::user();
        $veiculosregioes = null;
                $regioes = Regioes::leftJoin("modulos","recodigo", "moultimaregiao")
                    ->leftjoin("veiculos", "vemodulo", "mocodigo")
                    ->whereIn("recliente", $usuario->getEmpresasUsuario())
                    ->where("veplaca", "!=", null)
                    ->get();

        $veiculosregioes = [];
        foreach ($regioes as $i => $regiao) {
            if(isset($regiao->moultimaregiao)){
                $veiculosregioes[$regiao->moultimaregiao][] = $regiao;
            }
        }
        return response([
            "veiculosregioes"=>$veiculosregioes
        ]);
    }
}

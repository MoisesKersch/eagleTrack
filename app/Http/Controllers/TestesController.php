<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\RoteirizadorHelper;
use App\Models\Pontos;
use App\Models\Cliente;
use App\Models\Veiculo;
use App\Models\ItensRota;
use Auth;
use DB;
use App\Services\JornadaTrabalhoLivreService;

class TestesController extends Controller
{
    public function testeDefineRota(Request $request)
    {
    	$rotHelper = new RoteirizadorHelper;
    	$pontosModel = new Pontos();
    	$codEmpresas = [];
        $usuario = Auth::user();

    	if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }

        foreach ($clientes as $key => $cliente) {
        	$codEmpresas[] = $cliente->clcodigo;
        }

        $params = [
        	'annotations' => 'true'
        ];

    	// $pontos = $pontosModel->getPontos($codEmpresas);
    	$pontos = Pontos::select('polatitude', 'polongitude')->whereIn('pocodigocliente', $codEmpresas)->get();

    	return json_encode($rotHelper->defineRota($pontos));
    }

    public function testeCalculaDistanciaTempo(Request $request)
    {
        $origem = $request->origem;
        $destino = $request->destino;
        $rotHelper = new RoteirizadorHelper;

        return json_encode($rotHelper->calculaDistanciaTempo($origem, $destino));
    }

    public function testeBuscaPontoMaisProximo(Request $request)
    {
        //buscaPontoMaisProximo?origem=-52.6375502,-27.0963137
        $rotHelper = new RoteirizadorHelper;
        $codEmpresas = [];
        $origem = $request->origem ?: false;
        $pontos = $request->pontos ? json_decode($request->pontos) : false;

        if (\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        } else {
            $clientes = \Auth::user()->clientes;
        }

        foreach ($clientes as $key => $cliente) {
            $codEmpresas[] = $cliente->clcodigo;
        }

        if (!$pontos)
            $pontos = Pontos::select('pocodigo', 'polatitude', 'polongitude')->whereIn('pocodigocliente', $codEmpresas)->get();

        return $rotHelper->buscaPontoMaisProximo($origem, $pontos);
    }

    public function testeMontaCargas(Request $request)
    {
        //http://127.0.0.1:8000/painel/testes/montaCargas?clientes%5B%5D=22&pedidos%5B%5D=38&pedidos%5B%5D=39&pedidos%5B%5D=32&pedidos%5B%5D=33&veiculos%5B%5D=86

        //http://127.0.0.1:8000/painel/testes/montaCargas?clientes%5B%5D=22&pedidos%5B%5D=31&pedidos%5B%5D=38&pedidos%5B%5D=39&pedidos%5B%5D=32&pedidos%5B%5D=33&pedidos%5B%5D=36&pedidos%5B%5D=34&pedidos%5B%5D=37&pedidos%5B%5D=41&pedidos%5B%5D=89&veiculos%5B%5D=86&veiculos%5B%5D=59&veiculos%5B%5D=45
        if (\Auth::user()->usumaster == 'S') {
            $rotHelper = new RoteirizadorHelper();
            $clrequest = $request->clientes;
            $perequest = $request->pedidos;
            $verequest = $request->veiculos;
            $empresas = Cliente::select('clcodigo', 'clnome')->orderBy('clcodigo')->get();
            $veiculos = Veiculo::select('vecodigo', 'vedescricao', 'veproprietario')->orderBy('veproprietario')->get();
            $pedidos = ItensRota::select('ircodigo', 'ircliente', 'irnome')->orderBy('ircliente')->get();
            $result['ok'] = [];
            $result['erros'] = [];

            if ($clrequest && $perequest && $verequest) {
                $clientes = Cliente::select('clcodigo', 'cllatitude', 'cllongitude')->where('clcodigo','=', $clrequest)->orderBy('clcodigo')->get();

                $dadosPedidos = \DB::table('clientes as c')
                    ->select(DB::raw('ir.ircodigo, ir.ircodigoexterno, ir.irdocumento, ir.irnome, ir.irqtde, ir.ircubagem, ir.irpeso, ir.irvalor, p.poregiao, p.polatitude, p.polongitude, p.potipo'))
                    ->leftJoin('itens_rotas as ir', 'ir.ircliente', '=', 'c.clcodigo')
                    ->leftJoin('pontos as p', 'ir.ircodigoexterno', '=', 'p.pocodigoexterno')
                    ->where('p.pocodigocliente', '=', $clrequest)
                    ->whereIn('ir.ircodigo', $perequest)
                    ->get();

                $dadosVeiculos = \DB::table('clientes as c')
                    ->select(DB::raw('vecodigo, veplaca, vemaxpeso, vecubagem, veautonomia, vemaxentregas, vemaxhoras, vecusto, vevelocidademax, vetipo, vehorainiciotrabalho, vehorafinaltrabalho, veestradaterra, vebalsas, vepedagios, array(select vrregiao from veiculo_regiao where vrveiculo = vecodigo) as vrregiao'))
                    ->leftJoin('veiculos as v', 'c.clcodigo', '=', 'v.veproprietario')
                    ->where('c.clcodigo', '=', $clrequest)
                    ->whereIn('vecodigo', $verequest)
                    ->get();

                $result = $rotHelper->montaCargas($dadosPedidos->toArray(), $dadosVeiculos->toArray(), $clientes[0]);

            }

            return view('testes.montaCarga', compact('clientes', 'veiculos', 'pedidos', 'result', 'empresas'));
        } else {
            return (['mensagem' => 'Você não tem permissão para acessar essa página!', 'codigo' => '500']);
        }
    }

    public function testeJornadaLivre(Request $request)
    {
        $jtl = new JornadaTrabalhoLivreService;

        return $jtl->script($request);
    }
}

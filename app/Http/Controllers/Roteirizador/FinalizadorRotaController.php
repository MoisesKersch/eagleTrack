<?php

namespace App\Http\Controllers\Roteirizador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pontos;
use App\Models\ItensRota;
use App\Models\Veiculo;
use App\Models\Regioes;
use App\Models\Rota;
use App\User;
use App\Services\RoteirizadorService;
use App\Helpers\RoteirizadorHelper;
use App\Helpers\DataHelper;
use Datetime;


class FinalizadorRotaController extends Controller
{
    public function criar(Request $request)
    {
        $data = date('d/m/Y');
        $clienteRota = \Auth::user()->cliente->clcodigo;
        $buscar = false;
        if ($request->data) $data = $request->data;
        if ($request->cliente) $clienteRota = $request->cliente;
        if ($request->buscar) $buscar = true;

        $clientes = Cliente::select('clnome', 'clcodigo');
    		if(\Auth::user()->usumaster == 'N'){
    			$clientes->join('usuarios_clientes', 'uclcliente', '=', 'clcodigo')
    			->where('uclusuario', '=', \Auth::user()->id);
    		}
    	$clientes = $clientes->get();

    	return view('roteirizador.finalizacao.criar', compact('clientes', 'data', 'clienteRota', 'buscar'));
    }

    public function regioes(Request $request){
        $id = $request->clientes;
        $data = $request->data_saida;
        $regioes = Regioes::with('regioesCoordenadas')
            ->where('recliente',$id)
            ->get();

        $rotas = $this->Rotas($id,$data);

        return response([
            'regioes' => $regioes,
            'rotas' => $rotas
        ]);
    }

    public function Rotas($id,$data){
        $rotas = Rota::with(['veiculo','itensRota', 'itensRota.ponto' => function($query) use ($id) {
            $query->where('pocodigocliente', $id);
        }])
            ->where('rocliente',$id)
            ->where('rodata','=', $data)
            ->orderBy('rocodigo', 'ASC')->get();


        return $rotas;
    }

    /*
        Função responsavel por retornar os veiculos do cliente para mesclagem de rota e os
        pontos para indicar inicio e fim da rota.
    */
    public function getVeiculosCapacitados(Request $request)
    {
        $veiculos = Veiculo::select('rocodigo', 'ropeso', 'rocubagem', 'roplaca', 'veplaca', 'vemaxpeso', 'vecubagem')
            ->leftJoin('rotas', 'veplaca', '=', 'roplaca')
            ->where('vestatus','=','A')
            ->where('veproprietario','=', $request->cliente)
            ->where('rodata', '=', $request->data)
            ->orWhere('rodata', '=', null)
            ->where('vestatus','=','A')
            ->where('veproprietario','=', $request->cliente)
            ->orderBy('veplaca', 'ASC')
            ->get();

        $pontos = Pontos::where('pocodigocliente', $request->cliente)->get();

        return [
            'veiculos' => $veiculos,
            'pontos' => $pontos
        ];
    }

    public function alterarCorRota(Request $r){
        $rota = Rota::find($r->id);
        $rota->rocor = $r->cor;
        $rota->save();
    }

    /*
        Une duas ou mais rotas, víncula a um veículo e então exclui as rotas
    */
    public function mesclarRota(Request $request)
    {
        if (!$request)
            return ([
                'mensagem' => 'Erro ao receber dados, contate o administrador!',
                'erro' => 500
            ]);

        $rotHelper = new RoteirizadorHelper();
        $itensRota = ItensRota::select('ircodigoexterno', 'ircodigo')->whereIn('irrota', $request->rota)->get();
        $codigosExterno = [];
        $codigosPonto = [];

        foreach ($itensRota as $ir => $item) {
            $codigosPonto[] = $item->ircodigo;
        }

        $pontos = ItensRota::select('ircodigo as pocodigo', 'polatitude', 'polongitude')
            ->join('pontos', 'pocodigoexterno', '=', 'ircodigoexterno')
            ->whereIn('ircodigo', $codigosPonto)
            ->where('pocodigocliente', $request->cliente)
            ->orderBy('ircodigo', 'asc')
            ->get()
            ->toArray();

        $partida = explode('|', $request->partida);
        $retorno = explode('|', $request->retorno);
        array_unshift($pontos, [
            'pocodigo' => (int)str_replace("'", '', $partida[0]),
            'polatitude' => $partida[1],
            'polongitude' => $partida[2]
        ]);
        $pontos[] = [
            'pocodigo' => (int)str_replace("'", '', $retorno[0]),
            'polatitude' => $retorno[1],
            'polongitude' => $retorno[2]
        ];

        $params = [
            'overview' => 'full',
            'roundtrip' => 'false',
            'geometries' => 'geojson',//'polyline',
            'destination' => 'last',
            'source' => 'first',
            'steps' => 'true',
            //nao tinha
            'annotations' => 'true'
        ];

        $defineRota = $rotHelper->defineRota($pontos, $params);

        if (!$defineRota)
            return ([
                'mensagem' => 'Serviço indisponível no momento!',
                'erro' => 500
            ]);

        $dadosRota = $defineRota->trips[0]->legs[0]->steps[0];
        $waypoints = $defineRota->waypoints;
        $data = explode('/', $request->data);
        $helper = new DataHelper;

        try {
            $newRota = new Rota;
            $newRota->rodata = $data[2].'-'.$data[1].'-'.$data[0];
            $newRota->ropontosaida = $partida[0];
            $newRota->ropontoretorno = $retorno[0];
            $newRota->rostatus = 'P';
            $newRota->roplaca = $request->veiculo;
            $newRota->rocubagem = $request->cubagem;
            $newRota->roqtde = $request->volume;
            $newRota->rocliente = $request->cliente;
            $newRota->ropeso = $request->peso;
            $newRota->rovalor = $request->valor;
            $newRota->rotempo = $helper->converteSegundosEmFormatoHora($defineRota->trips[0]->duration);
            $newRota->rotemposegundos = $defineRota->trips[0]->duration;
            $newRota->rokm = $defineRota->trips[0]->distance;
            $idRota = $newRota->save();

            if ($idRota) {
                foreach ($pontos as $key => $ponto) {
                    ItensRota::where('ircodigo', $ponto['pocodigo'])
                        ->update([
                            'irplaca' => $request->veiculo,
                            'irstatus' => 'R',
                            'irrota' => $newRota->rocodigo,
                            'irordem' => $waypoints[$key]->waypoint_index
                        ]);
                }

                Rota::whereIn('rocodigo', $request->rota)->delete();
            }

            return ([
                'success' => 'Rotas mescladas com sucesso',
                'codigo' => 200,
                'rota' => $newRota
            ]);
        } catch (\Exception $e) {
            // return ([
            //     'mensagem' => $e->getMessage(),
            //     'erro' => 500
            // ]);
            return ([
                'mensagem' => 'Não foi possível mesclar as rotas, tente novamente mais tarde.',
                'erro' => 500
            ]);
        }
    }
}

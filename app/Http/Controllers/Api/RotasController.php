<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\RoteirizadorHelper;
use App\Http\Controllers\Api\PontosController;
use App\Helpers\DataHelper;
use App\Models\Cliente;
use App\Models\ItensRota;
use App\Models\Veiculo;
use App\Models\Pontos;
use App\Models\Rota;
use Response;


class RotasController extends Controller
{
    public function cadastrar(Request $request)
    {
    	$cliente = new Cliente();
    	$input = file_get_contents('php://input'); // Pega todos os dados do json
        $jsonDecode = json_decode($input);

    	$erro = [];
        if(!isset($jsonDecode->chave)){
            return Response::json([
                'status' => 'Chave de API Obrigatória!'
            ]);
        }
        else{
          $chave = $jsonDecode->chave;
            $codCliente = $cliente->getClienteApiKey($chave);
            if(!$codCliente){
                return response([
                	'status' => 'Chave Inválida!'
                ]);
            }
        }
        $rotas = $jsonDecode->rotas;

        foreach ($rotas as $i => $rota) {
        	$status = 'I';
        	$tempo = 0;
        	$cub = 0;
        	$qtde = 0;
        	$peso = 0;
        	$valor = 0;
        	$kms = 0;
        	$custo = 0;
        	$codExterno = [];
        	$novaRota = null;
        	$ordem = 0;
        	if($rota->cadastra_ponto) {
        		$pontos = '';
        		$saida[] = (array)$rota->saida_retorno[0];
        		$retorno[] = (array)$rota->saida_retorno[1];
        		foreach ($rota->itens as $j => $ponto) {
	        		$pontos[$j]['nome'] = $ponto->nome;
	        		$pontos[$j]['codigo'] = $ponto->codigo;
					$pontos[$j]['endereco'] = $ponto->endereco;
					$pontos[$j]['latitude'] = $ponto->latitude;
					$pontos[$j]['longitude'] = $ponto->longitude;
					$pontos[$j]['tipo'] = $ponto->tipo;
					$pontos[$j]['raio'] = $ponto->raio;
					$pontos[$j]['cubagem'] = $ponto->cubagem;
					$pontos[$j]['volumes'] = $ponto->volumes;
					$pontos[$j]['peso'] = $ponto->peso;
					$pontos[$j]['valor'] = $ponto->valor;
        		}
	        	$pt = new PontosController;
	        	$arrayPontos['chave'] = $chave;
	        	$arrayPontos['pontos'] = array_merge((array)$saida, $pontos, (array)$retorno);
	        	$erro['pontos'] = $pt->salvaPontos($arrayPontos, $codCliente);
        	}
        	$pla = Veiculo::where('veplaca', '=', $rota->placa)->where('veproprietario', '=', $codCliente)->first();
        	// dd($rota);

        	if(empty($rota->placa) || empty($pla)){
        		
        		$erro[$rota->itens[0]->codigo][] = "Código ".$rota->itens[0]->codigo." placa invalida!";
        	
        	}elseif($rota->criar_rota) {
	        	if(empty($rota->itens[0]->data)) {
	        		$erro[$rota->itens[0]->codigo][] = "Código ".$rota->itens[0]->codigo." data invalida!";
	        	}elseif(empty($rota->placa)){
	        		$erro[$rota->itens[0]->codigo][] = "Código ".$rota->itens[0]->codigo." placa invalida";
	        	}else{
	        		foreach ($arrayPontos['pontos'] as $j => $item) {
	        			if(!isset($item['cubagem'])) 
	        				continue;
	        			$codExterno[] = $item['codigo'];
	        			$cub = $cub + $item['cubagem'];
	        			$qtde = $qtde + $item['volumes'];
	        			$peso = $peso + $item['peso'];
	        			$valor = $valor + $item['valor'];
	        		}
	        		
	        		// sort($codExterno);
	        		// dd($codExterno);
	        		$pontos = Pontos::whereIn('pocodigoexterno', $codExterno)
	        			->where('pocodigocliente', '=', $codCliente)
	        			->get();
	        		$itens = [];
	        		foreach ($rota->itens as $k => $itt) {
	        			$itens[$itt->codigo] = $itt;
	        		}
	        		$arrayPontos = [];
	        		$poSaida['polatitude'] = $saida[0]['latitude'];
	        		$poSaida['polongitude'] = $saida[0]['longitude'];
		            $arrayPontos[] = $poSaida;
		            foreach ($pontos as $i => $itm) {
		                $arrayPontos[] = ['polatitude' => $itm->polatitude, 'polongitude' => $itm->polongitude];

		            }
		            $poRetorno['polatitude'] = $retorno[0]['latitude'];
	        		$poRetorno['polongitude'] = $retorno[0]['longitude'];
		            $arrayPontos[] = $poRetorno;

	        		$rotaHelper = new RoteirizadorHelper;
			        $rot = $rotaHelper->defineRota($arrayPontos,  ['overview' => 'full', 'roundtrip' => 'false','geometries' =>  'polyline', 'destination' => 'last', 'source' => 'first']);
			        if(!isset($rot->waypoints)) continue;
			        foreach($rot->waypoints as $k => $position) {
			        	if($position->waypoint_index == 0) {
			        		$inicio = $k;
			        	}elseif($position->waypoint_index == count($rot->waypoints) -1) {
			        		$fim = $k;	
			        	}
			        }

			        $rt = (array) $rot;
			        $Psaida = Pontos::where('pocodigoexterno', '=', $saida[0]['codigo'])
			        	->where('pocodigocliente', $codCliente)
			        	->first();
			        $Pretorno = Pontos::where('pocodigoexterno', '=', $retorno[0]['codigo'])
			        	->where('pocodigocliente', $codCliente)
			        	->first();

			        try {
			        	$kms = ($rt['trips'][0]->distance);
			        	$tempo = ($rt['trips'][0]->duration);
			        	$tempo = gmdate("H:i:s", $tempo);
			        	$route = new Rota;
			        	$route->rotempo = $tempo;
			        	$route->rokm = $kms;
			        	$route->rocubagem = $cub;
			        	$route->roqtde = $qtde;
			        	$route->ropeso = $peso;
			        	$route->rovalor = $valor;
			        	$route->rodata = $rota->itens[0]->data;
			        	$route->roplaca = $rota->placa;
			        	$route->rocliente = $codCliente;
			        	$route->ropontosaida = $Psaida->pocodigo;
			        	$route->ropontoretorno = $Pretorno->pocodigo;
			        	$route->rostatus = 'P';
			        	$route->save();
			        	$novaRota = $route->rocodigo;
			        	$status = "R";
			        	
			        } catch (\Exception $e) {
			        	return $e->getMessage();
			        }

			        // foreach ($pontos as $p => $ponto) {
			        // 	// $pts[$ponto->pocodigoexterno] = $ponto;
			        // 	$rti[$p] = $rt['waypoints'][$p]->waypoint_index;
			        // }
	        	}
        	}
        	foreach($pontos as $i => $item){
        		if($i == 1)
        		if(empty($itens[$item->pocodigoexterno])) continue;
	        	$cod = Pontos::where('pocodigoexterno', '=', $itens[$item->pocodigoexterno]->codigo)->where('pocodigocliente', '=', $codCliente)->first();
        		if(empty($itens[$item->pocodigoexterno]->data)) {
        			$erro[$item->pocodigoexterno][] = "Código ".$item->pocodigoexterno." data invalida!";
        		}elseif(!isset($itens[$item->pocodigoexterno]) || empty($itens[$item->pocodigoexterno]->codigo) || empty($cod)){
        			$erro[$item->podescricao] = "Item ".$item->podescricao." O campo Código é invalido!";
        		}else{
		        	try {
		        		$itensRota = new ItensRota();
		        		$itensRota->ircliente = $codCliente;
		        		$itensRota->irdata = $itens[$item->pocodigoexterno]->data;
		        		$itensRota->irnome = $item->podescricao;
		        		$itensRota->irplaca = $rota->placa;
		        		$itensRota->ircodigoexterno = $item->pocodigoexterno;
		        		$itensRota->irdocumento = $itens[$item->pocodigoexterno]->documento;
		        		$itensRota->irqtde = $itens[$item->pocodigoexterno]->volumes;
		        		$itensRota->ircubagem = (float) str_replace(',', '.', $itens[$item->pocodigoexterno]->cubagem);
		        		$itensRota->irpeso = $item->peso;
		        		$itensRota->irvalor = (float) str_replace(',', '.', $itens[$item->pocodigoexterno]->valor);
		        		$itensRota->irrota = $novaRota;
		        		$itensRota->irstatus = 'R';
		        		
		        		// $ordem = $rti[$i];
		        		$itensRota->irordem = $rot->waypoints[$i + 1]->waypoint_index;
		        		$itensRota->save();

		        	} catch(\Exception $e) {
		        		return $e->getMessage();
		        	}
        		}
        	}
        }
        return [
        	'erros' => $erro
        ];
    }
}

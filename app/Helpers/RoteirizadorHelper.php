<?php

namespace App\Helpers;
use App\Helpers\DataHelper;

class RoteirizadorHelper
{

	/*
		$arrayPontos = array com os pontos que o veiculo deve passar
		- Parametros
			- roundtrip (ida e volta) = true || false
			- source (fonte) = any || first
			- destination = any || last
			- steps (passos) = false || true - instruções de rota para cada viagem
			- annotations (anotações) = true , false (Padrão), nodes , distance , duration , datasources , weight , speed - retorna metadados
			- geometries = polyline (padrão), polyline6 , geojson - formato geometrico
			- overview (visao) = simplified (padrão), full , false

		- $arrayPontos = array com polatitude e polongitude dos pontos;
		- $params = array com os Parametros, $key = nomeDoParametro e $params = valor do parametro;

		- exemplos
			- url: 'http://192.168.10.2:5000/trip/v1/driving/13.388860,52.517037;13.397634,52.529407;13.428555,52.523219;13.418555,52.523215?source=first&destination=last&annotations=true'
			- pontos: -52.41,-26.8769611;-52.41,-26.8769611;-52.4102139472961,-26.8770525412239

		- limite de 100 coordenadas. Maior do que isso da erro de TooBig
	*/
		//do jeito que ela que
	public function defineRota($arrayPontos, $params = false)
    {
		$pontos = [];
		$pontosTmp = [];
		$countPontos = count($arrayPontos);
		$count = 0;

		if($countPontos < 1) {
			return ([
				'mensagem' => 'É necessário ter mais do que um ponto!',
				'codigo' => '500'
			]);
		}

		foreach ($arrayPontos as $k => $ponto) {
			$pontosTmp[] = str_replace("'", '', $ponto['polongitude']).','.str_replace("'", '', $ponto['polatitude']);
			$count++;
			if ($count == 100 || $countPontos-1 == $k) {
				$pontos[] = implode(";", $pontosTmp);
				$count = 0;
			}
        }

		if ($pontos == "" || !isset($pontos)) {
			return 'false';
		}



        foreach ($pontos as $key => $ponto) {
            $ur = 'http://services.eagletrack.com.br:5000';
            if ($params) {
				$url = $ur.'/trip/v1/driving/'.$ponto.'?';
				foreach ($params as $key => $value) {
					//$url .= $key == count($params)-1 ? $key.'='.$value : $key.'='.$value.'&';
					$url .= $key.'='.$value.'&';
                }
                $url = trim($url, '&');
			} else {
				$url = $ur.'/trip/v1/driving/'.$ponto.'?roundtrip=true&source=first&steps=true&destination=any&annotations=false&geometries=geojson&overview=full';
            }

			$ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = json_decode(curl_exec($ch));
            $info = curl_getinfo($ch);
            curl_close($ch);

			return $result;
		}
	}

//do jeito que tu passou
	public function defineRotaRoute($arrayPontos, $params = false)
	{
		$pontos = [];
		$pontosTmp = [];
		$countPontos = count($arrayPontos);
		$count = 0;

		if($countPontos < 1) {
			return ([
				'mensagem' => 'É necessário ter mais do que um ponto!',
				'codigo' => '500'
			]);
		}

		foreach ($arrayPontos as $k => $ponto) {

			$pontosTmp[] = $ponto['polongitude'].','.$ponto['polatitude'];
			$count++;
			if ($count == 100 || $countPontos-1 == $k) {
				$pontos[] = implode(";", $pontosTmp);
				$count = 0;
			}
		}

		if ($pontos == "" || !isset($pontos)) {
			return 'false';
		}

		$results;

		foreach ($pontos as $key => $ponto) {
	        	$ur = 'http://services.eagletrack.com.br:5000';
			if ($params) {
				$url = $ur.'/route/v1/driving/'.$ponto.'?';
				foreach ($params as $key => $value) {
					// $url .= $key == count($params)-1 ? $key.'='.$value : $key.'='.$value.'&';
					$url .= $key.'='.$value.'&';
				}
				$url = trim($url, '&');
			} else {
				$url = $ur.'/route/v1/driving/'.$ponto.'?continue_straight=false&alternatives=false&steps=true&geometries=geojson&overview=full';
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$result = json_decode(curl_exec($ch));
			$info = curl_getinfo($ch);
			curl_close($ch);

			$results[] = $result;

		}
		return $results;
	}

 	/*
		- parametros
			$origem = (string) latitude, longitude
			$destino = (string) latitude, longitude
			$params = array()
				- alternatives (alternativas) => false, true, numero
				- steps (passos) => passos da rota para cada 'perna' de rota
				- annotations (anotações) => true, false(Padrão), nodes, distance, duration, datasources, weight, speed
				- geometries => polyline (default), polyline6 , geojson
				- overview (Visão geral) => simplified (default), full , false
				- continue_straight (continue em frente) => default (default), true , false

		- exemplo
			http://192.168.10.2:5000/route/v1/driving/13.388860,52.517037;13.397634,52.529407;13.428555,52.523219?overview=false'

	*/
	public function calculaDistanciaTempo($origem, $destino, $params = false, $inteiro = false)
	{
		$resultado = [];
		$dataHelper = new DataHelper;

		if (!isset($origem) || !isset($destino)) {
			return ([
				'mensagem' => 'É necessário ter um ponto de origem e destino!',
				'codigo' => '500'
			]);
		}

		if ($params) {
			$url = 'http://services.eagletrack.com.br:5000/trip/v1/driving/'.$origem.';'.$destino.'?';
			foreach ($params as $key => $value) {
				$url .= $key == count($params)-1 ? $key.'='.$value : $key.'='.$value.'&';
			}
		} else {
			$url = 'http://services.eagletrack.com.br:5000/route/v1/driving/'.$origem.';'.$destino.'?geometries=geojson';
		}

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        if (!$result) return array(['mensagem' => 'Nenhum dado encontrado!', 'codigo' => '500']);

        foreach ($result->routes as $key => $value) {
        	$resultado[] = array(
        		'distancia' => $this->distanciaMetros($value->distance, $inteiro),
        		'distSemTratamento' => $value->distance,
        	    'tempoEstimado' => $dataHelper->converteSegundosPorExtenso($value->duration),
        	    'coordenadas' => $value->geometry->coordinates
        	);
        }

        return $resultado;
	}

	public function distanciaMetros($metros, $inteiro = false)
	{
		$tmp = $metros/1000;

		if ($tmp < 1) {
			return $inteiro ? $metros : $metros.' m(s).';
		}

		$e = explode('.', $tmp);

		return $inteiro ? $tmp : $e[0].' km(s) e '.substr(chunk_split($e[1], 3, ','),0,-1).' m(s).';
	}

	/*
		$origem = (string)-52.6375502,-27.0963137 {deve ser invertido lat e lon}
		$arrayPontos = array([
			'codigo' => 1,
			'polatitude' => -52.6375502,
			'polongitude' => -27.0963137
		])
	*/
	public function buscaPontoMaisProximo($origem, $arrayPontos)
	{
		$ponto = [];
		$pontosTmp = [];
		$countPontos = count($arrayPontos);
		$count = 0;
		$menorDistanciaTmp = 0;
		$menorDistancia = 99999999999999;
		$return = false;

		if (!$origem) {
			return ([
				'mensagem' => 'É necessário ter um ponto de origem!',
				'codigo' => '500'
			]);
		}

		if ($countPontos < 1 || !is_array($arrayPontos) ? !is_array($arrayPontos->toArray()) : false) {
			return ([
				'mensagem' => 'É necessário ter mais do que um ponto!',
				'codigo' => '500'
			]);
		}

		foreach ($arrayPontos as $key => $value) {
			$vpDestino = isset($value['polongitude']) ? $value['polongitude'].','.$value['polatitude'] : $value[0].','.$value[1];
			$menorDistanciaTmp = $this->calculaDistanciaTempo($origem, $vpDestino, false, true);

			if (isset($menorDistanciaTmp[0]['mensagem'])) continue;
			foreach ($menorDistanciaTmp as $k => $v) {
				if ($v['distSemTratamento'] < $menorDistancia && $origem != $vpDestino) {
					$menorDistancia = $v['distSemTratamento'];
					$return = $value;
				}
			}
		}

		if (!$return) return array(['mensagem' => 'Nenhum ponto próximo encontrado!', 'codigo' => '500']);

		return $return;
	}

	/*
		$pedidos = array de pedidos (tabela itens_rotas);
		$veiculo = array de veiculos (tabela de veiculos);
		$cliente = ['codigoCliente', 'latitude', 'longitude']; // indica o ponto de partida
	*/

	public function montaCargas($pedidos, $veiculos, $cliente = false, $verificar = true)
	{
		$cargaMontada = [];
		$cmInconsistencias = [];
		$peso = 0;
		$cubagem = 0;
		$valor = 0;
		$pedidosTmp = [];
		$pTmp = [];
		$verificar = false;

		/* Tratamento de erros */

		if (in_array(0, array_column((array)$pedidos, 'irpeso'))) {
			$zeradosp = [];
			foreach ($pedidos as $p) {
				$p->irpeso == 0 ? $zeradosp[] = $p: '';
			}

			return [
				'mensagem' => 'Existem pedidos com peso zerado',
				'codigo' => '500',
				'pedidos' => $zeradosp
			];
		}

		if (in_array(0, array_column((array)$pedidos, 'ircubagem'))) {
			$zeradosv = [];
			foreach ($pedidos as $p) {
				$p->ircubagem == 0 ? $zeradosv[] = $p : '';
			}

			return [
				'mensagem' => 'Existem pedidos com cubagem zerada',
				'codigo' => '500',
				'pedidos' => $zeradosv
			];
		}

		if ($verificar) {
			$sumCubagemP = array_sum(array_column($pedidos, 'ircubagem'));
			$sumCubagemV = array_sum(array_column($veiculos, 'vecubagem'));
			if ($sumCubagemP > $sumCubagemV) {
				return ([
					'mensagem' => 'O número de pedidos excede a cubagem do número de veículos disponíveis',
					'codigo' => '500',
					'diferenca' => $sumCubagemP-$sumCubagemV
				]);
			}

			$sumPesoP = array_sum(array_column($pedidos, 'irpeso'));
			$sumPesoV = array_sum(array_column($veiculos, 'vemaxpeso'));
			if ($sumPesoP > $sumPesoV) {
				return ([
					'mensagem' => 'O peso dos pedidos excede o limite do peso dos veículos disponíveis',
					'codigo' => '500',
					'diferenca' => $sumPesoP-$sumPesoV
				]);
			}
		}
		/* Fim tratamento de erros */

		$cont = 0;
		$cargas = [];
		while (count($pedidos) > 0 && count($veiculos) > 0) {

			$result = $this->carregaTodosOsVeiculos($pedidos, $veiculos, $cliente);

			$retorno = $this->retornaMelhorCarga($result);
			$pedidos = $pedidos->whereNotIn('ircodigo', array_keys($retorno['itens']));
			$veiculos = $veiculos->whereNotIn('veplaca', $retorno['placa']);
			$cargas[] = $retorno;

			$pedidos = count($pedidos) == 0 ? true : $pedidos;
			// if(empty($result)) break;

			return [
				'pedidos' => $pedidos,
				'cargas' => $cargas
			];
		}
	}
	
	public function carregaTodosOsVeiculos($pedidos, $veiculos, $cliente)
	{
		foreach($pedidos as $p => $pedido) {
			$arrayRegiaoP[isset($pedido->poregiao) ? $pedido->poregiao : 'semregiao'][] = $pedido;
		}


		//separa os veiculos por regiao, podendo duplicar o veiculo, caso tenha mais regioes
		foreach ($veiculos as $p => $veiculo) {
			// $regiaoV = [];
			// if(isset($veiculo['vrregiao']))
				$regiaoV = str_replace(['{','}'], ['', ''],explode(',', $veiculo->vrregiao));

			foreach ($regiaoV as $rv) {
				$arrayRegiaoV[$rv ? : 'semregiao'][] = $veiculo;
			}
		}
		$peso = 0;
		$cubagem = 0;
		$valor = 0;
		$regiaoVeiculoPedidos = [];
		//adiciona todos os pedidos que tem peso e cubagem menor que o veiculo individualmente
		// if(empty($arrayRegiaoP)) {
		// 	var_dump($pedidos); exit();
		// }
		foreach ($arrayRegiaoP as $key => $regiaoPedido) { //regioes por pedido
			// var_dump($arrayRegiaoV[$key], 'asdf');
			if (!isset($arrayRegiaoV[$key])) continue; //se nao tiver veiculo nessa regiao, continue
			foreach ($arrayRegiaoV[$key] as $k => $arvk) { //percorre os veiculos da regiao do pedido
				foreach ($regiaoPedido as $p => $rp) { //percorre os pedidos dessa regiao
					//verifica os pedidos que cabem no veiculo (nao acumulativo)
					if ((float)$rp->irpeso <= (float)$arvk->vemaxpeso && (float)$rp->ircubagem <= (float)$arvk->vecubagem) {
						$regiaoVeiculoPedidos[$arvk->veplaca]['veiculo'] = $arvk;
						$regiaoVeiculoPedidos[$arvk->veplaca][$rp->ircodigo] = $rp;
					}
				}
			}
		}
		$tmpPV = [];//pedidos nos veiculos temporariamente
		$peso = 0;
		$cubagem = 0;
		$contador = 0;
		//combinações
		$kgCarregados = 0;
		$cubCarregado = 0;
		foreach ($regiaoVeiculoPedidos as $v => $veiculo) { //pedidos que cabem no veiculo (nao acumulativo)

			foreach ($veiculo as $p => $pedido) { //percorre os pedidos
				if ($p == 'veiculo') continue;
				$kgCarregados = $pedido->irpeso;
				$tmpPV[$v][$contador][$pedido->ircodigo] = $pedido;
				$capVeiculo = (float)$veiculo['veiculo']->vemaxpeso;
				$cubVeiculo = (float)$veiculo['veiculo']->vecubagem;
				$contador++;
				foreach ($veiculo as $p2 => $pedido2) { //percorre os pedidos para ver se cabe mais de um
					if ($p2 == 'veiculo' || $p == $p2) continue;
					$peso = (float)$pedido->irpeso+(float)$pedido2->irpeso;
					$cubagem = (float)$pedido->ircubagem+(float)$pedido2->ircubagem;
					$kgc = $kgCarregados+$pedido2->irpeso;
					$cub = $cubCarregado+$pedido2->ircubagem;

					if ($cub <= $kgc && $cubVeiculo <= $capVeiculo && (float)$peso <= $capVeiculo && (float)$cubagem <= $cubVeiculo) {
						$tmpPV[$v][$contador][$pedido2->ircodigo] = $pedido2;
						$kgCarregados = $kgCarregados+$pedido2->irpeso;
						$cubCarregado = $cubCarregado+$pedido2->ircubagem;

					}
				}
			}
		}

		//monta a rota e calcula gastos
		foreach ($tmpPV as $v => $veiculo) {
			foreach ($veiculo as $c => $carga) {
				// if($c  != 1) continue;
				$custoViagem = 99999999999999;
				$tmpPontos = [];
				if ($cliente)
					$tmpPontos[] = [
						'polatitude' => $cliente['cllatitude'],
						'polongitude' => $cliente['cllongitude']
					];
				$tmpPV[$v][$c]['totais']['peso'] = 0;
				$tmpPV[$v][$c]['totais']['cubagem'] = 0;
				$tmpPV[$v][$c]['totais']['valor'] = 0;
				foreach ($carga as $p => $pedido) {
					$tmpPontos[] = [
						'polatitude' => $pedido->polatitude,
						'polongitude' => $pedido->polongitude,
						'codigo' => $p
					];
					$tmpPV[$v][$c]['totais']['peso'] += (float)$pedido->irpeso;
					$tmpPV[$v][$c]['totais']['cubagem'] += (float)$pedido->ircubagem;
					$tmpPV[$v][$c]['totais']['valor'] += (float)$pedido->irvalor;
				}

				$rota = $this->defineRota($tmpPontos);
				// dd($rota);
				$tmpPV[$v][$c]['totais']['entregas'] = $rota;

				if ($rota) {
					foreach ($rota->waypoints as $rt => $waypoint) {
						if (isset($tmpPontos[$waypoint->waypoint_index]['codigo']))
							$tmpPV[$v][$c]['totais']['entregas']->sequencia[] = $tmpPontos[$waypoint->waypoint_index]['codigo'];
					}

					$custoViagem = $this->distanciaMetros($rota->trips[0]->distance, true)*(float)$regiaoVeiculoPedidos[$v]['veiculo']->vecusto;
				}

				$tmpPV[$v][$c]['totais']['gastos'] = $custoViagem;
				$tmpPV[$v][$c]['totais']['lucro'] = $tmpPV[$v][$c]['totais']['valor']-$custoViagem;
			}
		}

		//remove os veiculos com mais gastos
		$result = [];
		$menorCusto = [];
		foreach ($tmpPV as $k => $placa) {
			$maiorLucro = 0;
			foreach ($placa as $c => $carga) {
				if ($carga['totais']['lucro'] > $maiorLucro) {
					$result[$k] = $carga;
					$maiorLucro = $carga['totais']['lucro'];
				}
			}
		}
		return $result;
	}

	public function retornaMelhorCarga($cargas)
	{
		$maiorLucro = 0;
		$melhorCarga = [];
		$placa = [];
		foreach($cargas as $h => $carga) {
			if ($carga['totais']['lucro'] > $maiorLucro) {
				$result[$h] = $carga;
				$maiorLucro = $carga['totais']['lucro'];
				$melhorCarga = $carga;
				$placa = $h;
			}
		}

		return ['itens' => $melhorCarga, 'placa' => $placa];
	}
}

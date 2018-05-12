<?php
namespace App\Helpers;

use App\Helpers\MapaHelper;
use App\Models\Pontos;

class PontosHelper
{
    //---Relacionar posição com ponto próximo,
	public function relacionaPosicaoAPontoMaisProximo($pontos, $posicoesParado){
			$helper = new MapaHelper();

			foreach ($posicoesParado as $key => $posicao) {
				$latlog = explode(',', $posicao->bilatlog);

				foreach ($pontos as $ponto) {
					//pegar a latitude e longitude do bilhete e calcular distancia entre bilhete e ponto
					$distancia = round($helper->calculaDistanciaLatLon($latlog[0], $latlog[1], $ponto['polatitude'], $ponto['polongitude']),2);
					//converter km em metros;
					//----------------------------------------------------------
                    $distancia = $distancia * 1000;
                    //dd($ponto['poraio'], $distancia);
					//--- caso distancia seja menor que raio do ponto, atribui sua descrção para este ponto
                    $posicoesParado[$key]->proximo = 'Nada consta!';
					if($distancia < $ponto['poraio']){
						$posicoesParado[$key]->proximo = $ponto['podescricao'];
						break;
					}
				}
			}
		return $posicoesParado;
    }

    public function buscaPontoProximo($lat, $lon, $mot, $cli)
    {
        $pontos = Pontos::select('podescricao', 'polatitude', 'polongitude', 'poraio', 'potipo')
            ->leftJoin('motorista_ponto', 'pocodigo', '=', 'mpponto')
            ->where('pocodigocliente', '=', $cli);
        if($mot > 0) {
            $pontos->where('mpmotorista', '=', $mot);
        }
        $pontos = $pontos->get();

        $localDist = '';
        foreach($pontos as $p) {
            $distancia = $this->calculaDistanciaLatLon($p->polatitude, $p->polongitude, $lat, $lon)*1000;
            if($distancia < $p->poraio) {
                $local = $p->podescricao;
                $tipo = $p->potipo;

                //DEIXAR DISTANCIA COMO STRING;
                //$localDist = (int) $distancia;
                $localDist = $distancia;
            }
        }
        if(empty($localDist)) {
            return
                [
                    'nome' => 'Não identificado',
                    'dist' => 0,
                    'tipo' => ''
                ];
        }else {
            return
                [
                    'nome' => $local,
                    'dist' => $localDist,
                    'tipo' => $tipo
                ];
        }
    }

    //ordena pontos por proximidade retornda distancia em Km;
    public function ordenaPontosProximidade($request)
    {
        $pontos = Pontos::select('pocodigo', 'podescricao', 'potipo', 'pocodigocliente', 'poendereco','poraio', 'polatitude', 'polongitude')
        ->where('pocodigocliente', '=', \Auth::user()->usucliente)
        ->get();

		$pt = [];

        foreach ($pontos as $i => $p) {
            $distancia = $this->calculaDistanciaLatLon($p->polatitude, $p->polongitude, $request->lat, $request->lng);
            $pt[$i]['distancia'] = round($distancia, 2);
            $pt[$i]['pocodigo'] = $p->pocodigo;
            $pt[$i]['podescricao'] = $p->podescricao;
            $pt[$i]['potipo'] = $p->potipo;
            $pt[$i]['pocodigocliente'] = $p->pocodigocliente;
            $pt[$i]['poendereco'] = $p->poendereco;
            $pt[$i]['poraio'] = $p->poraio;
            $pt[$i]['polatitude'] = $p->polatitude;
            $pt[$i]['polongitude'] = $p->polongitude;
        }
        sort($pt);
        return $pt;

        // return $pontos;
    }

    public function calculaDistanciaLatLon($lat1, $lon1, $lat2, $lon2)
    {
        if($lat1 == $lat2 && $lon1 == $lon2) {
            return 0;
        }else {
            $d2r = 0.017453292519943295769236;
            $dlon = ($lon2 - $lon1) * $d2r;
            $dlat = ($lat2 - $lat1) * $d2r;

            $temp_sin = sin($dlat/2.0);
            $temp_cos = cos($lat1 * $d2r);
            $temp_sin2 = sin($dlon/2.0);

            $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
            $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

            return 6368.1 * $c;
        }
    }

    /*
        Busca endereço no serviço do nominatim
    */
    public static function buscaEnderecoNominatim($query)
    {
        $q = str_replace(' ', '+', $query);
        $url = 'http://nominatim.eagletrack.com.br/nominatim/search.php?q='.$q.'&format=json&addressdetails=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = json_decode(curl_exec($ch));
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $result;
    }
}

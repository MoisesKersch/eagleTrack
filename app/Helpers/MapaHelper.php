<?php
namespace App\Helpers;

class MapaHelper
{
    private $pointOnVertex = true;
    public function somaDistancia($lat1, $long1, $lat2, $long2)
    {
        if(($lat1 == $lat2) && ($long1 == $long2)){
            return 0;
        }else{
            $d2r = 0.017453292519943295769236;
            $dlong = ($long2 - $long1) * $d2r;
            $dlat = ($lat2 - $lat1) * $d2r;

            $temp_sin = sin($dlat/2.0);
            $temp_cos = cos($lat1 * $d2r);
            $temp_sin2 = sin($dlong/2.0);

            $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
            $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

            return 6368.1 * $c;
        }
    }


    public function calculaDistanciaLatLon($lat1, $long1, $lat2, $long2){

        if(($lat1 == $lat2) && ($long1 == $long2)){
            return 0;
        }else{
            $d2r = 0.017453292519943295769236;
            $dlong = ($long2 - $long1) * $d2r;
            $dlat = ($lat2 - $lat1) * $d2r;

            $temp_sin = sin($dlat/2.0);
            $temp_cos = cos($lat1 * $d2r);
            $temp_sin2 = sin($dlong/2.0);

            $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
            $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

            return 6368.1 * $c;

        }
    }

    /*
    *  Recebe um endereço da tabela bilhetes
    *  Retorna um array separando cidade - UF do endereço, no formato [endereco][ Cidade - UF ]
    */
    public function quebraEnderecoCidadeUF($endereco){
        $endereco = explode(",",$endereco);
        $pos = count($endereco) - 1;
		$enderecoInverso = array_reverse($endereco);
		$cidade = $endereco[$pos];
		//remove cidade e estado do endereco
		array_pop($endereco);//remove cidade/estado
		$endereco = implode(",", $endereco);//une elementos em uma string
        return array($endereco,$cidade);
    }
    /*
    *   Recebe 2 arrays, 1º com latitude, longitude, 2º array com pontos para comparar
    */
    public function buscarPontoProximo($latlog,$arrayPontos){
        $localDist;
        $local;
        foreach($arrayPontos as $p){
            $distancia = $this->calculaDistanciaLatLon($latlog[0],$latlog[1],$p['polatitude'],$p['polongitude'])*1000;
            if($distancia <= $p['poraio']){
                $local = $p['podescricao'];
                $localDist = (int)$distancia;
            }
        }
        //verifica se tem dados
        if(empty($localDist)){
            return false;
        }else{
            return array("nomePonto" => $local,
                         "distancia" => $localDist
                        );
        }
    }

    function pointInPolygon($point, $polygon, $pointOnVertex = true)
    {
        $this->pointOnVertex = $pointOnVertex;

        // Transform string coordinates into arrays with x and y values
        // $point = $this->pointStringToCoordinates($point);
        $vertices = array();
        foreach ($polygon as $vertex) {
            if(isset($vertex['rclatitude'])){
                $vertex['lat'] = $vertex['rclatitude'];
                $vertex['lng'] = $vertex['rclongitude'];
            }
            $vertices[] = ['x' => $vertex['lat'], 'y' => $vertex['lng']];
        }

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);
        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1];
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['log'] and $point['lat'] > min($vertex1['x'], $vertex2['x']) and $point['lat'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['log'] > min($vertex1['y'], $vertex2['y']) and $point['log'] <= max($vertex1['y'], $vertex2['y']) and $point['lat'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['log'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['lat']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['lat'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon.
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }
    function pointOnVertex($point, $vertices) {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }

    }

    function pointStringToCoordinates($pointString) {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }
}

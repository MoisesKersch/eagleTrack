<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Helpers\RoteirizadorHelper;
use DB;
use Excel;

class VeiculoService{

    public function rastroQuery(Request $request){
        $placa = $request->placa;
        $dataInicial = $request->dataIni;
        $dataFinal = $request->dataFim;
        $matched = $request->matched;

        $rotas = \DB::table('bilhetes as b')
            ->select(DB::raw("b.bilatlog as posicao,max(b.bidataevento)
            as data,b.biendereco as endereco,v.vecodigo,coalesce(b.bivelocidade,'0')
            as velocidade,(b.bihodometro/1000) as hodometro"))
            ->leftJoin('veiculos as v', 'biplaca', '=', 'veplaca')
            ->where('biplaca','=', $placa)
            ->where('bidataevento', '>', $dataInicial)
            ->where('bidataevento', '<', $dataFinal)
            ->groupBy('b.bilatlog', 'b.biendereco', 'v.vecodigo', 'b.bivelocidade', 'b.bihodometro', 'b.bidataevento')
            ->orderBy('b.bidataevento')
            ->get();

        return $rotas;
    }

    public function rotaVeiculo(Request $request)
    {
        $rotas = $this->rastroQuery($request);

        return response([
            'array' => $rotas
        ]);
    }

    public function rastroCorrigidoVeiculo(Request $request)
    {
        $rotas = $this->rastroQuery($request);
        $posicoes_corrigidas = array();
        $arrayPontos = array();
        $ap = array();
        $count = 0;
        $old_key = null;

        //Colocar a primeira posição do veículo no array
        $array = explode(',', $rotas[0]->posicao);
        array_push($ap, array('polongitude' => $array[1], 'polatitude' => $array[0]));

        foreach ($rotas as $key => $ponto) {
            if($count > 99){
                array_push($arrayPontos, $ap);
                $ap = array();
                $count = 0;
            }
            if($old_key == null){
                $count++;
                $array = explode(',', $ponto->posicao);
                array_push($ap, array('polongitude' => $array[1], 'polatitude' => $array[0]));
            }else{
                //Verificar apenas a posição anterior e a posterior
                if(((isset($rotas[$key-1]) && $ponto->endereco == $rotas[$key-1]->endereco) &&
                    (isset($rotas[$key+1]) && $ponto->endereco == $rotas[$key+1]->endereco)) && $ponto->endereco != '')
                {
                    $count++;
                    $array = explode(',', $ponto->posicao);
                    array_push($ap, array('polongitude' => $array[1], 'polatitude' => $array[0]));
                }
            }
            $old_key = $key;
        }

        //adicionar a ultima localizacao do item no array. Para que o rastro vá até onde o ítem se encontra
        $array = explode(',', $rotas[count($rotas)-1]->posicao);
        array_push($ap, array('polongitude' => $array[1], 'polatitude' => $array[0]));

        array_push($arrayPontos, $ap);
            $rHelp = new RoteirizadorHelper();
            $results = array();
            foreach ($arrayPontos as $key => $ap) {
                array_push($results,$rHelp->defineRotaRoute($ap));
            }

            foreach ($results as $key => $result) {
                if($result == null || !isset($result->routes)){
                    continue;
                }
                foreach ($result->routes[0] as $key => $legs){
                    if ($legs != null && is_array($legs)) {
                        foreach ($legs as $key => $leg) {
                            foreach ($leg->steps as $key => $step) {
                                foreach ($step->geometry->coordinates as $key => $item) {
                                    $aux = $item[0];
                                    $item[0] = $item[1];
                                    $item[1] = $aux;
                                    array_push($posicoes_corrigidas,$item);
                                }

                            }
                        }
                    }
                }
            }
        return response([
            'posicoes_corrigidas' => $posicoes_corrigidas,
            'posicoes' => $rotas,
        ]);
    }

    public function excessosVelocidades(Request $request)
    {
        $placa = $request->placa;
        $dataInicial = $request->dataIni;
        $dataFinal = $request->dataFim;

        $rotas = \DB::table('bilhetes as b')
            ->select(DB::raw("b.bilatlog as posicao,b.bidataevento
            as data,coalesce(b.biendereco, 'Sem endereço') as endereco,
            v.vecodigo,coalesce(b.bivelocidade,'0') as velocidade,
            (b.bihodometro/1000) as hodometro, coalesce(v.vevelocidademax, 80)
            as velocidademax"))
            ->leftJoin('veiculos as v', 'biplaca', '=', 'veplaca')
            ->where('biplaca','=', $placa)
            ->where('bidataevento', '>', $dataInicial)
            ->where('bidataevento', '<', $dataFinal)
            ->where('bivelocidade', '>', 50)
            ->orderBy('b.bidataevento')
            ->get();

        return response ([
            'array' => $rotas
        ]);
    }

}

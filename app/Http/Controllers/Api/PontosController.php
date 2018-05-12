<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use App\Models\Pontos;
use App\Models\UsuarioApp;
use App\Models\Cliente;
use App\Helpers\PontosHelper;

class PontosController extends Controller{

    public function cadastroJson(Request $r){
        $array = $r->all();
        // $arrayResponse = array('status' => 'OK');

        if(!isset($array['chave'])){
            return Response::json([
                'status' => 'Chave de API Obrigatória!'
            ]);
        }else{
            $chave = $array['chave'];
            $codCliente = Cliente::getClienteApiKey($chave);
            if(!$codCliente){
                return Response::json([
                    'status' => 'Chave de API Inválida!'
                ]);
            }
        }

        $arrayResponse = $this->salvaPontos($array);
        return Response::json([
          'status'=> $arrayResponse
        ]);
    }

    public function salvaPontos($array, $codCliente)
    {
        $arrayResponse = array();
        foreach ($array['pontos'] as $i => $ponto){

            // if($ponto['nome'] != 'IDEMAR FALCAO ME') continue;

            $oldPontos = null;

            $p = new Pontos();
            $p->podescricao = $ponto['nome'];
            $p->pocodigocliente = $codCliente;
            $p->pocodigoexterno = $ponto['codigo'];

            //check RAIO
            if(isset($ponto['raio']) && isset($ponto['raio']) > 0){
                $p->poraio = $ponto['raio'];
            }else{
                $p->poraio = 50;
            }

            //check Location
            if(!empty($ponto['latitude']) && !empty($ponto['longitude'])){
                $p->polatitude = $ponto['latitude'];
                $p->polongitude = $ponto['longitude'];
            }

            //check Tipo
            if(isset($ponto['tipo'])){
                $p->potipo = $ponto['tipo'];
            }else{
                $p->potipo = 'C';
            }

            //check ENDERECO
            if(!empty($ponto['endereco'])){
                $p->poendereco = $ponto['endereco'];
                if(empty($ponto['latitude']) && empty($ponto['longitude'])){
                    $endereco = PontosHelper::buscaEnderecoNominatim($ponto['endereco']);
                    if($endereco != []){
                        $p->polatitude = $endereco[0]->boundingbox[0];
                        $p->polongitude = $endereco[0]->boundingbox[2];
                    }else{
                        array_push($arrayResponse, array($ponto['codigo']=>'Endereço insuficiente para o ponto: '.$ponto['nome']));
                        continue;
                        // return Response::json([
                        //   'status'=>'Endereço insuficiente para o ponto: '.$ponto['nome']
                        // ]);
                    }
                }
            }else{
                if(!empty($ponto['latitude']) && $ponto['latitude'] != '' && !empty($ponto['longitude']) && $ponto['longitude'] != ''){
                    //Call nomination to set address;
                    $endereco = PontosHelper::buscaEnderecoNominatim($ponto['latitude'].','.$ponto['longitude']);
                    if($endereco != []){
                        $p->poendereco = $endereco[0]->display_name;
                    }
                }else{
                    array_push($arrayResponse, array($ponto['codigo']=>'Faltando campo obrigatório!'));
                    continue;
                }
            }

            if(!isset($ponto['codigo'])){
                array_push($arrayResponse, array($ponto['codigo']=>'Faltando campo obrigatório!'));
                continue;
            }else{
                $oldPontos = Pontos::where('pocodigoexterno', $ponto['codigo'])->where('pocodigocliente', $codCliente)->limit(1)->get();
                if(count($oldPontos)>0){
                    foreach ($oldPontos as $key => $oldPonto) {
                        isset($ponto['tipo']) ? $oldPonto->potipo = $p->potipo : '';
                        $p->podescricao != null ? $oldPonto->podescricao = $p->podescricao : '';
                        $p->polatitude != null ? $oldPonto->polatitude = $p->polatitude:'';
                        $p->polongitude != null ? $oldPonto->polongitude = $p->polongitude:'';
                        $p->poendereco != null ? $oldPonto->poendereco = $p->poendereco:'';
                        $p->poraio != null ? $oldPonto->poraio = $p->poraio:'';
                        // dd('OldPonto', $oldPonto, $p);
                        $oldPonto->save();
                    }
                }
                if($oldPontos->isEmpty()){
                    $p->save();
                }
            }
            array_push($arrayResponse, array($ponto['codigo']=>'OK'));

        }
            return $arrayResponse;
    }
}

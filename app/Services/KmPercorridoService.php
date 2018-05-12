<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use DB;
use App\Helpers\MapaHelper;
use App\Helpers\DataHelper;
use App\Helpers\PdfHelper;
use Excel;

class KmPercorridoService
{

    public function query($request)
    {
        $data_inicio = $request['data_inicio'];
        $hora_inicio = $request['hora_inicio'];
        $data_fim = $request['data_fim'];
        $hora_fim = $request['hora_fim'];
        $veiculos = $request['buscarVeiculos'];
        if(empty($veiculos)){
            $vei = Veiculo::whereIn('veproprietario', $request->clientes)->get();
            $veiculos = '';
            foreach($vei as $v) {
                $veiculos .= $v->vecodigo.',';
            }
            $veiculos = trim($veiculos, ',');
        }
        $grupo_motorista = $request['grupo_motorista'];

        $dados = null;

        $date = \DateTime::createFromFormat('d/m/Y H:i', $data_inicio." ".$hora_inicio);
        $datahorainicio = $date->format('Y-m-d H:i');
        $date = \DateTime::createFromFormat('d/m/Y H:i', $data_fim." ".$hora_fim);
        $datahorafim = $date->format('Y-m-d H:i');


        $condicao = $this->checkCondition($grupo_motorista, $datahorainicio , $datahorafim);

        if($condicao){
          if($veiculos == null || $veiculos == 'undefined'){
            $veiculos = Veiculo::select('vecodigo')->where('veproprietario', '=', \Auth::user()->usucliente)
                ->where('vestatus', '=', 'A')
                ->get();
          }else if(!is_array($veiculos)){
            $veiculos = explode(',', $veiculos);
          }

          $dados = DB::table('bilhetes')
              ->select('biplaca','veprefixo','vedescricao')
              ->addSelect(DB::raw(" to_char (bidataevento, 'dd/mm/yyyy') as data "))
              ->addSelect(DB::raw(" (max(coalesce(bihodometro, 0)) - min(coalesce(bihodometro, 0)))/1000 as total"))
              ->join('veiculos','veiculos.veplaca','=','bilhetes.biplaca')
              ->where('bidataevento', '>', $datahorainicio)
              ->where('bidataevento', '<', $datahorafim)
              ->whereIn('veiculos.vecodigo', $veiculos)
              ->groupBy('biplaca','data', 'vedescricao','veprefixo')
              ->orderBy('biplaca', 'ASC');
              $dados = $dados->get();

            foreach ($dados as $key => $dado) {
                if($dados[$key]->total == 0){
                    unset($dados[$key]);
                }
            }

            $ant = null;
           foreach ($dados as $key => $dado) {
               if($ant != null ){
                   // TODO Agrupar por dia
                   if($ant->biplaca == $dado->biplaca && $ant->data == $dado->data){
                       $dado->total = $dado->total + $dado->total;
                       unset($dados[$key-1]);
                   }
               }
               $ant = $dado;
           }

           $dados = $this->agruparPlacas($dados);

        }
      return $dados;
    }


    public function agruparPlacas($dados){

        $placas = null;
        $old = '';
        //Agrupa por placa
        $j = 0;
        $i = 0;

        foreach($dados as $kmp) {
            if($i == 0){
                 $old_placa = $kmp->biplaca;
            }
            if($kmp->biplaca == $old_placa) {
                $placas[$kmp->biplaca][$j]['biplaca'] = $kmp->biplaca;
                $placas[$kmp->biplaca][$j]['veprefixo'] = $kmp->veprefixo;
                $placas[$kmp->biplaca][$j]['vedescricao'] = $kmp->vedescricao;
                $placas[$kmp->biplaca][$j]['data'] = $kmp->data;
                $placas[$kmp->biplaca][$j]['total'] = $kmp->total;
                $j = $j + 1;
            }else{
                $j = 0;
            }
            $old_placa = $kmp->biplaca;
            $i = $i + 1;
        }

        return $placas;
    }


    public function checkCondition($grupo_motorista , $datahorainicio , $datahorafim){
        $placas = 0;
        if($grupo_motorista >= 0){
          $placas = DB::table('veiculos')
                      ->select('veiculos.veplaca', 'veiculos.vecodigo')
                      ->join('motoristas', 'motoristas.mtcodigo', '=', 'veiculos.vemotorista')
                      ->join('grupo_motorista', 'grupo_motorista.gmcodigo', '=', 'motoristas.mtgrupo')
                      ->where('motoristas.mtgrupo','=',$grupo_motorista)
                      ->count();
        }

        if($grupo_motorista == -1){
          if($datahorainicio < $datahorafim){
            return true;
          }
        }else{
          if($placas = 0){
            return false;
          }
          else if($datahorainicio < $datahorafim){
            return true;
          }
        }

      return false;
    }


}

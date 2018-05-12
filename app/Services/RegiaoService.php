<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use DB;
use Excel;

class RegiaoService
{

    public function query($request)
    {
        $data_inicio = $request['data_inicio'];
        $hora_inicio = '00:00';
        $data_fim = $request['data_fim'];
        $hora_fim = '23:59';
        $clientes = $request['clientes_regioes'];
        $veiculos = $request['regiao_veics'];
        $regioes = $request['regiao_regioes'];
        $dados = null;
        $date = \DateTime::createFromFormat('d/m/Y H:i', $data_inicio." ".$hora_inicio);
        $datahorainicio = $date->format('Y-m-d H:i');
        $date = \DateTime::createFromFormat('d/m/Y H:i', $data_fim." ".$hora_fim);
        $datahorafim = $date->format('Y-m-d H:i');
        $myv =  implode(",",$veiculos);
        $myr = implode(",",$regioes);
        $dados = DB::table('veiculos')
            ->select('biplaca','biregiao','redescricao', 'mtnome as motorista', 'veprefixo')
            // ->addSelect(DB::raw(" to_char (bidataevento, 'dd/mm/yyyy') as data "))
            ->addSelect(DB::raw(" (max(bihodometro) - min(bihodometro))/1000  as kms "))
            ->addSelect(DB::raw(" round(avg(bivelocidade),2)  as velocidade_media "))
            ->addSelect(DB::raw(" count(bimotivotransmissao)  as qtd_paradas "))
            ->addSelect(DB::raw(" to_char (min(bidataevento), 'dd/mm/yyyy') as data_entrada "))
            ->addSelect(DB::raw(" to_char (max(bidataevento), 'dd/mm/yyyy') as data_saida "))
            ->addSelect(DB::raw(" to_char (min(bidataevento), 'dd/mm/yyyy hh24:mi') as hora_entrada "))
            ->addSelect(DB::raw(" to_char (max(bidataevento), 'dd/mm/yyyy hh24:mi') as hora_saida "))
            ->join('bilhetes','veplaca','=','biplaca')
            ->join('regioes','recodigo','=','biregiao')
            ->join('motoristas','mtcodigo','bimotorista')
            ->where('bidataevento', '>=', $datahorainicio)
            ->where('bidataevento', '<=', $datahorafim)
            ->where('bimotivotransmissao',22)
            ->whereIn('veiculos.vecodigo', $veiculos)
            ->whereIn('recodigo', $regioes)
            ->groupBy('biplaca','biregiao','redescricao','motorista','veprefixo')
            ->orderBy('biplaca','ASC');

        $dados = $dados->get();
        $dados = $this->agruparPlacas($dados);

        return $dados;
    }


    //Agrupa por placa
    public function agruparPlacas($dados){
        $placas = null;
        $old = '';
        $j = 0;
        $i = 0;

        foreach($dados as $reg) {
            $reg->biplaca = $reg->biplaca." | ".$reg->veprefixo;
            if($i == 0){
                 $old_placa = $reg->biplaca;
            }

            $placas[$reg->biplaca][$j]['biplaca'] = $reg->biplaca;
            $placas[$reg->biplaca][$j]['redescricao'] = $reg->redescricao;
            // $placas[$reg->biplaca][$j]['data'] = $reg->data;
            $placas[$reg->biplaca][$j]['velocidade_media'] = $reg->velocidade_media;
            $placas[$reg->biplaca][$j]['qtd_paradas'] = $reg->qtd_paradas;
            $placas[$reg->biplaca][$j]['data_entrada'] = $reg->data_entrada;
            $placas[$reg->biplaca][$j]['data_saida'] = $reg->data_saida;
            $placas[$reg->biplaca][$j]['hora_entrada'] = $reg->hora_entrada;
            $placas[$reg->biplaca][$j]['hora_saida'] = $reg->hora_saida;
            $placas[$reg->biplaca][$j]['kms'] = $reg->kms;
            $placas[$reg->biplaca][$j]['biregiao'] = $reg->biregiao;
            $placas[$reg->biplaca][$j]['motorista'] = $reg->motorista;

            if($reg->biplaca == $old_placa) {
                $j = $j + 1;
            }else{
                $j = 0;
            }
            $old_placa = $reg->biplaca;
            $i = $i + 1;
        }

        return $placas;
    }


}

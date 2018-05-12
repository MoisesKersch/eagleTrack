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

class MotoristaAjudanteService{

    public static function desassociarMA($id, $flg_motorista){
        if($flg_motorista == true){
            $veiculos = Veiculo::select('vecodigo','veplaca','veprefixo','vemotorista','veajudante')
                ->where('vemotorista', '=', $id)
                ->whereNotNull('vemotorista')
                ->get();
            foreach ($veiculos as $key => $veiculo) {
                $veiculo->vemotorista = null;
                $veiculo->save();
                // MotoristaAjudanteService::setBimotorista(0,$veiculo, 10);
                // MotoristaAjudanteService::setIvmotorista(0,$veiculo, 10);
            }
        }else{
            $veiculos = Veiculo::select('vecodigo', 'veplaca','veprefixo','vemotorista','veajudante')
                ->where('veajudante',$id)
                ->whereNotNull('veajudante')
                ->get();
            foreach ($veiculos as $key => $veiculo) {
                $veiculo->veajudante = null;
                $veiculo->save();
                // MotoristaAjudanteService::setBiajudante(0, $veiculo, 10);
                // MotoristaAjudanteService::setIvajudante(0, $veiculo, 10);
            }
        }
    }

    // @ivmotorista ivmotorista para ser setado na tabela ignicao_veiculos
    // @veiculo veiculo para ser setado
    public static function setIvmotorista($ivmotorista, $veiculo, $motivo){
        DB::select(DB::raw("UPDATE public.ignicao_veiculos
              SET ivmotorista = $ivmotorista
              where  ivplaca = '".$veiculo->veplaca."'
          and (select exists (select ivdataevento from ignicao_veiculos where ivplaca = '".$veiculo->veplaca."' and ivdataevento >= '".date('Y-m-d')."' and ivmotivotransmissao = $motivo order by ivdataevento desc limit 1))
          and ivdataevento >= (select ivdataevento from ignicao_veiculos where ivplaca = '".$veiculo->veplaca."' and ivmotivotransmissao = $motivo order by ivdataevento desc limit 1)"));
    }

    // @ivajudante ivajudante para ser setado na tabela ignicao_veiculos
    // @veiculo veiculo para ser setado
    public static function setIvajudante($ivajudante, $veiculo, $motivo){
        DB::select(DB::raw("UPDATE public.ignicao_veiculos
              SET ivajudante = $ivajudante
              where  ivplaca = '".$veiculo->veplaca."'
          and (select exists (select ivdataevento from ignicao_veiculos where ivplaca = '".$veiculo->veplaca."' and ivdataevento >= '".date('Y-m-d')."' and ivmotivotransmissao = $motivo order by ivdataevento desc limit 1))
          and ivdataevento >= (select ivdataevento from ignicao_veiculos where ivplaca = '".$veiculo->veplaca."' and ivmotivotransmissao = $motivo order by ivdataevento desc limit 1)"));
    }

    // @bimotorista motorista para ser setado
    // @veiculo veiculo para ser setado
    public static function setBimotorista($bimotorista, $veiculo, $motivo){
        DB::select(DB::raw("UPDATE public.bilhetes
            SET bimotorista = $bimotorista
            where  biplaca = '".$veiculo->veplaca."'
        and (select exists (select bidataevento from bilhetes where biplaca = '".$veiculo->veplaca."' and bidataevento >= '".date('Y-m-d')."' and bimotivotransmissao = $motivo order by bidataevento desc limit 1))
        and bidataevento >= (select bidataevento from bilhetes where biplaca = '".$veiculo->veplaca."' and bimotivotransmissao = $motivo order by bidataevento desc limit 1)"));
    }

    // @biajudante ajudante para ser setado
    // @veiculo veiculo para ser setado
    public static function setBiajudante($biajudante, $veiculo, $motivo){
        DB::select(DB::raw("UPDATE public.bilhetes
            SET biajudante = $biajudante
            where  biplaca = '".$veiculo->veplaca."'
        and (select exists (select bidataevento from bilhetes where biplaca = '".$veiculo->veplaca."' and bidataevento >= '".date('Y-m-d')."' and bimotivotransmissao = $motivo order by bidataevento desc limit 1))
        and bidataevento >= (select bidataevento from bilhetes where biplaca = '".$veiculo->veplaca."' and bimotivotransmissao = $motivo order by bidataevento desc limit 1)"));

    }
}

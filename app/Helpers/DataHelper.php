<?php
namespace App\Helpers;

use Closure;
use DateTime;

class DataHelper
{
    public static function converteDataMySqlParaPostGres($data){
      //converte data
      $aux = explode(" ", $data);//separa data hora
      $data_data = $aux[0];//recebe data
      $data_hora = $aux[1];//recebe hora

      $aux2 = explode("-",$data_data);
      $ano = $aux2[0];
      $mes = $aux2[1];
      $dia = $aux2[2];

      $dataConvertida = $dia."/".$mes."/".$ano." ".$data_hora;

      return $dataConvertida;
    }

    public function dataBrToUS($data){
        $a = explode(" ",$data);
        $b = explode("/",$a[0]);
        $c = $b[1]."/".$b[0]."/".$b[2]." ".$a[1];
        return $c;
    }
    /*
    *@Param  datetime $data
    *@Param integer $dias
    *
    * OBS: Funcaos para subtrair dias de uma data
    */
    public function subtrairData($data,$dias){

        //converta para formato americano
        $dataUS = $this->dataBrToUS($data);

        //converte dias em segundos
        $segundos = $dias * 86400;
        //converte para timestamp
        $timestamp = strtotime($dataUS);
        //subtrai a quantidade de dias
        $y = $timestamp - $segundos;
        //retorna data subtraida
        return date("d/m/Y G:i:s",$y);
    }
    /********************************************
    *******FUNCAO CALCULAR DIFERENCA DE TEMPO ***
    ********************************************/
    public function diferencaDatas($dataInicial, $dataFinal){
        //verifica se ja em padrao americado
        if(strpos($dataInicial, '-')){
            $dataUSIni = $dataInicial;
            $dataUSFim = $dataFinal;
        }else{
            //converte para formato americano
            $dataUSIni = $this->dataBrToUS($dataInicial);
            $dataUSFim = $this->dataBrToUs($dataFinal);
        }
        $timeStampIni = strtotime($dataUSIni);
        $timeStampFim = strtotime($dataUSFim);
        $segundos = $timeStampFim - $timeStampIni;
        return $segundos;
    }

    //Diferença de datas com Datetime
    function diffDatas($dataInicial, $dataFinal, $formato = null)
    {
        $dataInicial = new DateTime($dataInicial);
        $dataFinal = new DateTime($dataFinal);
        $diff = $dataInicial->diff($dataFinal)->format($formato ?: "%H:%I");

        return $diff;
    }

    public static function somaHora($horas)
    {
            $seconds = 0;
            foreach ( $horas as $i => $time ){
                list( $g, $i, $s ) = explode( ':', $time );
                $seconds += $g * 3600;
                $seconds += $i * 60;
                $seconds += $s;
            }
            $hours = floor( $seconds / 3600 );
            $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
            $seconds -= $hours * 3600;
            $minutes = floor( $seconds / 60 );
            $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
            $seconds -= $minutes * 60;
            $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);
            return "{$hours}:{$minutes}:{$seconds}";
        }

    public function converteSegundosPorExtenso($segundos){
        $retorno = "";
        //converte dias
        $dias = $segundos/86400;
        if($dias >= 1){
            $d = (int) $dias;
            $dias = $dias - (int)$dias;
            $retorno .= $d." dia(s), ";
        }
        //converte horas
        $horas = $dias*24;
        if($horas >= 1){
            $h = (int)$horas;
            $horas = $horas - (int)$horas;
            $retorno .= $h." hora(s), ";
        }
        $minutos = $horas*60;
        if($minutos >= 1){
            $m = (int)$minutos;
            $retorno .= $m." minuto(s).";
        }
        if(empty($retorno)) $retorno = "Menos de 1 minuto.";
        return $retorno;
    }

    /*
    *   VERIFICA SE INTERVALO SE ENCONTRA DENTRO DE OUTRO INTERVALO
    *   RETORNA:
    *   0 - TOTALMENTE FORA
    *   1 - TOTALMENTE DENTRO
    *   2 - SOBREESCREVE TODO O INTERVALO
    *   3 - PARCIALMENTE DENTRO INICIO
    *   4 - PARCIALMENTE DENTRO FINAL
    */
    public function verificaDentroIntervalo($horaI, $horaF, $iniIntervalo, $fimIntervalo){
        $horaI           = strtotime($horaI);
        $horaF           = strtotime($horaF);
        $iniIntervalo    = strtotime($iniIntervalo);
        $fimIntervalo    = strtotime($fimIntervalo);

        if($horaI < $iniIntervalo && $horaF > $fimIntervalo)
            return 2;
        elseif(($horaI > $iniIntervalo && $horaI < $fimIntervalo) && ($horaF > $iniIntervalo && $horaF < $fimIntervalo))
            return 1;
        elseif($horaI <= $iniIntervalo && ($horaF > $iniIntervalo && $horaF <= $fimIntervalo))
            return 3;
        elseif(($horaI > $iniIntervalo && $horaI < $fimIntervalo) && $horaF > $fimIntervalo)
            return 4;
        else
            return 0;
    }
    public function converteSegundosEmFormatoHora($segundos)
    {
        $retorno = "";
        //converte dias
        $dias = $segundos/86400;
        if($dias >= 1){
            $d = (int) $dias;
            $dias = $dias - (int)$dias;
            // $retorno .= $d.' ';
        }
        //converte horas
        $horas = $dias*24;
        if($horas >= 1){
            $h = (int)$horas;
            $horas = $horas - (int)$horas;
            $retorno .= $h.":";
        }
        $minutos = $horas*60;
        if($minutos >= 1){
            $m = (int)$minutos;
            $min = ($m < 10 ? '0'.$m : $m);
            if (isset($h))
                $retorno .= $min;
            else
                $retorno .= '00:'.$min.':00';
        }

        if(empty($retorno)) $retorno = "00:00:00";
        return $retorno;
    }
}

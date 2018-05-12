<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Motorista;
use App\Models\IgnicaoVeiculos;
use App\Helpers\DataHelper;
use App\Models\Feriado;
use App\Models\FechamentoFolha;
use DateTime;
use DB;
use Excel;

class JornadaTrabalhoService
{
    /*
    *   RECEBE A DATA POSTERIOR AO DIA DE INTERESSE, EXEMPLO:
    *   SE QUERO OS DADOS DO DIA 02/01/2017, ENTAO PASSo COMO PARAMETRO 03/01/2017
    */
    public function carregaDados($dataPosterior)
    {
        $dataHelper = new DataHelper;
        $dia = $dataPosterior;
        $dia = new \Datetime($dia ? : '');
        $data = date('Y-m-d', strtotime('-1 days', strtotime($dia->format('Y/m/d'))));
        $numDia = date('w', strtotime($data)); //numero dia da semana de 0 a 6
        $final= $dia->format('Y/m/d').' 00:00:00';
        $inicio = $data.' 00:00:00';
        $dFinal = $data.' 23:59:59';
        // $feriado = $this->verificaFeriado($data);

        //BILHETES MOTORISTAS

        $bilhetesMot = IgnicaoVeiculos::select(DB::raw("ivdataevento, ivmotorista as codigo, ivmotivotransmissao, ivcliente, ivponto, potipo, ivajudante, phponto as pontoExcessao, 'M' as tipo"))
                                      ->leftJoin('pontos_hora_espera_cliente', 'phponto', '=', 'ivponto')
                                      ->leftJoin('pontos', 'pocodigo', '=', 'ivponto')
                                      ->where('ivdataevento', '>=', $inicio)
                                      ->where('ivdataevento', '<=', $final)
                                      ->where('ivmotorista', '>', 0);

        //BILHETES AJUDANTES
        $bilhetesAju = IgnicaoVeiculos::select(DB::raw("ivdataevento, ivajudante as codigo, ivmotivotransmissao, ivcliente, ivponto, potipo, ivajudante, phponto as pontoExcessao, 'A' as tipo"))
                                      ->leftJoin('pontos_hora_espera_cliente', 'phponto', '=', 'ivponto')
                                      ->leftJoin('pontos', 'pocodigo', '=', 'ivponto')
                                      ->where('ivdataevento', '>=', $inicio)
                                      ->where('ivdataevento', '<=', $final)
                                      ->where('ivajudante', '>', 0)
                                      ->union($bilhetesMot)
                                      ->orderBy('ivdataevento','ASC')
                                      ->get();

        if($bilhetesAju->isEmpty()){
            return 'Nenhuma informação encontrado!';
        }
        //AGRUPAR BILHETES POR MOTORISTA/AJUDANTES
        $bilhetes = [];
        foreach($bilhetesAju as $bilhete){
            $bilhetes[$bilhete->codigo][] = $bilhete;
        }
        //CORRIGE IGNICAO DUPLICADA/ PRIMEIRA E ULTIMA IGNICAO VIRADA MEIA NOITE
        foreach($bilhetes as $key => $bilhete){
            $motivoAnterior = 0;
            if($bilhete[0]['ivmotivotransmissao'] == 10){
                    $x = array(
                                'ivdataevento'        => $inicio,
                                'codigo'              => $bilhete[0]['codigo'],
                                'ivmotivotransmissao' => 9,
                                'ivcliente'           => $bilhete[0]['ivcliente'],
                                'ivponto'             => $bilhete[0]['ivponto'],
                                'potipo'              => $bilhete[0]['potipo'],
                                'ivajudante'          => $bilhete[0]['ivajudante'],
                                'pontoexcessao'       => $bilhete[0]['pontoExcessao'],
                                'tipo'                => $bilhete[0]['tipo'],
                                );
                        array_unshift($bilhetes[$key], $x);
                }
            $tam = count($bilhete) - 1;
            if( $bilhete[$tam]['ivmotivotransmissao'] == 9){
                $x = array(
                            'ivdataevento'        => $dFinal,
                            'codigo'              => $bilhete[$tam]['codigo'],
                            'ivmotivotransmissao' => 10,
                            'ivcliente'           => $bilhete[$tam]['ivcliente'],
                            'ivponto'             => $bilhete[$tam]['ivponto'],
                            'potipo'              => $bilhete[0]['potipo'],
                            'ivajudante'          => $bilhete[0]['ivajudante'],
                            'pontoexcessao'       => $bilhete[0]['pontoExcessao'],
                            'tipo'                => $bilhete[$tam]['tipo']
                            );
                $bilhetes[$key][] = $x;
            }
            foreach($bilhete as $key2 => $b){
                if($motivoAnterior == $b['ivmotivotransmissao']){
                    unset($bilhetes[$key]);
                }
                $motivoAnterior = $b['ivmotivotransmissao'];
            }
        }

        //CODIGOS MOTORISTAS/AJUDANTES
        $codigosMotoristas = [];
        foreach($bilhetes as $key => $b){
            $codigosMotoristas[] = $key;
        }
        //OBTER DADOS MOTORISTAS/AJUDANTES
        $funcionarios = Motorista::select(
                                            'mtcodigo as codigo',
                                            'mtjornada as codigoJornada',
                                            'hjtiniprimeirot as inicioPrimeiroTurno',
                                            'hjtfimprimeirot as fimPrimeiroTurno',
                                            'hjtinisegundot as inicioSegundoTurno',
                                            'hjtfimsegundot as fimSegundoTurno',
                                            'hjttotalhoras as totalHoras',
                                            'hjtdsr as dsr',
                                            'hjtintervalo as totalIntervalo',
                                            'cljornadamotoristacomajudante as regraMotoristaComAjudante',
                                            'cljornadamotoristasemajudante as regraMotoristaSemAjudante',
                                            'cljornadaajudante as regraAjudante',
                                            'jttipo as tipoJornada'
                                            )
                                ->selectRaw("'M' as perfil")
                                // ->selectRaw("'M' as perfil, '$feriado' as feriado")
                                ->leftJoin('horas_jornada_trabalho', 'hjtjornada', '=', 'mtjornada')
                                ->leftJoin('jornada_trabalho', 'jtcodigo', '=', 'mtjornada')
                                ->leftJoin('clientes', 'clcodigo', '=', 'mtcliente')
                                ->where('mtstatus', '=', 'A')
                                ->where('hjtdiasemana', '=', $numDia )
                                ->whereIn('mtcodigo', $codigosMotoristas)
                                ->get();

        //AGRUPAMENTO FINAL, DADOS BILHETES + DADOS MOT/AJU + JORNADA + PERFIL MOT/AJU
        foreach ($funcionarios as $key => $f) {
            $funcionarios[$key]['bilhetes'] = $bilhetes[$f->codigo];
            $funcionarios[$key]['perfil']   = $bilhetes[$f->codigo][0]['tipo'];
            $funcionarios[$key]['data']     = $data;
        }
        return $funcionarios;
    }
    /*-------------------------------------------------------------*/
    /*            FUNCAO PRINCIPAL DO SERVICE                      */
    /*-------------------------------------------------------------*/
    public function calculaHoras($dataPosterior){
        $horarios = $this->carregaDados($dataPosterior);
        
        foreach ($horarios as $i => $horas) {
            $totalTrabalhada = $this->calculaHoraTrabalhada($horas);
            $falta = $this->calculaHoraFalta($horas, $totalTrabalhada);
            $adicNoturno = $this->adicNoturno($horas);
            $extraNoturno = $this->extraNoturno($horas);
            $feriado = $this->verificaFeriado($horas);
            $numDia = date('w', strtotime($horas['bilhetes'][0]['ivdataevento']));
            $horasCemPorCento = 0;
            if($horas->dsr == $numDia || !empty($feriado)) {
                $horasCemPorCento = 1;
            }
            // echo "Código/Perfil: ".$horas['codigo']."/".$horas['perfil']."<br>";
            // echo "Tipo Jornada: ".$horas['tipoJornada']."<br>";
            // echo "Trabalhada: ".$totalTrabalhada."<br>";
            $totalHoraExtra = $this->calculaHoraExtra($horas, $totalTrabalhada);
            // echo "Extra: ".$totalHoraExtra."<br>";
            $totalHoraEspera = $this->calculaHoraEspera($horas);
            // echo "Espera: ".$totalHoraEspera."<br>";
            $totalIntervalo = $this->calculaIntervalo($horas);
            // echo "Intervalo: ".$totalIntervalo."<br>";
            $totalHoras = $this->calculaTotalHoras($horas);
            // echo "Total: ".$totalHoras."<br>";
            // echo "---------------------------------------<br>";
            try {
                $fechamento = new FechamentoFolha;
                $fechamento->fehoratrabalhada = $totalTrabalhada;
                $fechamento->fehorastotal = $totalHoras;
                $fechamento->fedataentrada = $horas['bilhetes'][0]['ivdataevento'];
                $fechamento->fefimexpediente = $horas['bilhetes'][count($horas['bilhetes'])-1]['ivdataevento'];
                $fechamento->femotorista = $horas['codigo'];
                $fechamento->fedsr = $horasCemPorCento;
                $fechamento->fehoraextra = $totalHoraExtra;
                $fechamento->feextranoturno = $extraNoturno;
                $fechamento->fehoranoturna = $adicNoturno;
                $fechamento->fehorafalta = $falta;
                $fechamento->fehoraespera = $totalHoraEspera;
                $fechamento->feintervalo = $totalIntervalo;
                $fechamento->save();

                // echo "Gravado com sucesso.<br>";
            } catch(\Exception $e) {
                echo 'Erro ao salvar </br>';
                // var_dump($e->getMessage());exit;
            }
        }

    }
    //CALCULA TOTAL DE HORA EXTRA NORMAL
    public function calculaHoraExtra($horas, $totalTrabalhada){
        $dataHelper = new DataHelper;
        $extras = [];
        //JORNADA FIXA
        if($horas['tipoJornada'] == 'F'){
            $tam = count($horas['bilhetes']) - 1;
            $intervaloAnulado = false;
            $inicioAdicionalNoturnoManha  = $horas['data']." "."00:00";
            $fimAdicionalNorturnoManha    = $horas['data']." "."05:00";
            $inicioAdicionalNorturnoNoite = $horas['data']." "."22:00";
            $fimAdicionalNorturnoNoite    = $horas['data']." "."23:59";
            $inicioPrimeiroTurno    = $horas['data']." ".$horas['inicioPrimeiroTurno'];
            $fimPrimeiroTurno       = $horas['data']." ".$horas['fimPrimeiroTurno'];
            $inicioSegundoTurno     = $horas['data']." ".$horas['inicioSegundoTurno'];
            $fimSegundoTurno        = $horas['data']." ".$horas['fimSegundoTurno'];
            //anula intervalo refeicao caso dentro de adicional noturno manha
            if(strtotime($fimPrimeiroTurno) && strtotime($inicioSegundoTurno) < strtotime($fimAdicionalNorturnoManha)){
                // echo "ANULA INTERVALO".$fimPrimeiroTurno.$inicioSegundoTurno.$fimAdicionalNorturnoManha."<br>";
                $intervaloAnulado = true;
            }
            elseif(strtotime($fimPrimeiroTurno) < strtotime($fimAdicionalNorturnoManha) && strtotime($inicioSegundoTurno) > strtotime($fimAdicionalNorturnoManha)){
                $inicioSegundoTurno = $fimAdicionalNorturnoManha;
            }
            //anula intervalo refeicao caso dentro de adicional noturno noite
            if(strtotime($fimPrimeiroTurno) && strtotime($inicioSegundoTurno) > strtotime($inicioAdicionalNorturnoNoite)){
                $intervaloAnulado = true;
                // echo "ANULA INTERVALO".$fimPrimeiroTurno.$inicioSegundoTurno.$inicioAdicionalNorturnoNoite."<br>";
            }
            elseif(strtotime($inicioSegundoTurno) < strtotime($inicioAdicionalNorturnoNoite) && strtotime($fimSegundoTurno) > strtotime($inicioAdicionalNorturnoNoite)){
                $inicioSegundoTurno = $inicioAdicionalNorturnoNoite;
            }
            $horaInicial            = $horas['bilhetes'][0]['ivdataevento'];
            $horaFinal              = $horas['bilhetes'][$tam]['ivdataevento'];
            //VERIFICA HORAS TRABALHADAS NO INTERVALO E AD NOTURNO
            foreach($horas['bilhetes'] as $key => $h){
                //MOTORISTA
                if($horas['perfil'] == 'M'){
                    // echo "PERFIL MOTORISTA<br>";
                    if($h['ivmotivotransmissao'] == 9){
                        $agora  = strtotime($h['ivdataevento']);
                        $agoraF = strtotime($horas['bilhetes'][$key + 1]['ivdataevento']);
                        //INTERVALO
                        if(!is_null($horas['inicioSegundoTurno'])){
                            $tipoIntervalo = $dataHelper->verificaDentroIntervalo(
                                                                                $h['ivdataevento'],
                                                                                $horas['bilhetes'][$key + 1]['ivdataevento'],
                                                                                $fimPrimeiroTurno,
                                                                                $inicioSegundoTurno
                                                                                );
                        }else{
                            $tipoIntervalo = 0;
                        }
                        // echo "Tipo: ".$tipoIntervalo."<br>";
                        if($tipoIntervalo == 1){
                            // echo "DENTRO DE INTERVALO<br>";
                            if(!$intervaloAnulado)
                                $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $horas['bilhetes'][$key + 1]['ivdataevento']);
                        }
                        elseif($tipoIntervalo == 2){
                            // echo "INTERVALO: ".$fimPrimeiroTurno."/".$inicioSegundoTurno."<br>";
                            // echo "TODO INTERVALO: ".$h['ivdataevento']."/".$horas['bilhetes'][$key + 1]['ivdataevento']."<br>";

                            //SE PEGAR OS DOIS TURNOS INTEIROS
                            if($agora < strtotime($inicioPrimeiroTurno) && $agoraF > strtotime($fimSegundoTurno) && !$intervaloAnulado){
                                // echo "COBRE OS DOIS TURNOS<br>";
                                $extras[] = $dataHelper->diferencaDatas($fimPrimeiroTurno, $inicioSegundoTurno);
                                $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioPrimeiroTurno);
                                $extras[] = $dataHelper->diferencaDatas($fimSegundoTurno, $horas['bilhetes'][$key + 1]['ivdataevento']);
                            }
                            elseif(!$intervaloAnulado)
                                $extras[] = $dataHelper->diferencaDatas($fimPrimeiroTurno, $inicioSegundoTurno);
                        }
                        elseif($tipoIntervalo == 3){
                            // echo "PARCIALMENTE INICIO<br>";
                            if(!$intervaloAnulado)
                                $extras[] = $dataHelper->diferencaDatas($fimPrimeiroTurno, $horas['bilhetes'][$key + 1]['ivdataevento']);
                        }
                        elseif($tipoIntervalo == 4){
                            // echo "PARCIALMENTE FINAL<br>";
                            if(!$intervaloAnulado)
                                $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioSegundoTurno);
                            if($agoraF > strtotime($fimSegundoTurno) && $agoraF < strtotime($inicioAdicionalNorturnoNoite)){
                                $extras[] = $dataHelper->diferencaDatas($fimSegundoTurno, $horas['bilhetes'][$key + 1]['ivdataevento']);
                            }
                            elseif($agoraF > strtotime($fimSegundoTurno) && $agoraF > strtotime($inicioAdicionalNorturnoNoite)){
                                $extras[] = $dataHelper->diferencaDatas($fimSegundoTurno, $inicioAdicionalNorturnoNoite);
                            }
                        }
                        elseif($tipoIntervalo == 0){
                            // echo "TOTALMENTE FORA INT.<br>";
                            //PRE PRIMEIRO TURNO
                            if(!is_null($horas['inicioSegundoTurno'])){
                                if($agora < strtotime($fimAdicionalNorturnoManha) && $agoraF > strtotime($fimAdicionalNorturnoManha)){
                                    // echo "PRE PRIMEIRO TURNO - PARCIAL INICIO<br>";
                                    $extras[] = $dataHelper->diferencaDatas($fimAdicionalNorturnoManha, $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora > strtotime($fimAdicionalNorturnoManha) && $agora < strtotime($inicioPrimeiroTurno) && $agoraF > strtotime($inicioPrimeiroTurno)){
                                    // echo "MAIOR QUE AD NOT MANHA, ENTRA 1 TURNO".$h['ivdataevento']."/".$horas['bilhetes'][$key + 1]['ivdataevento']."<br>";
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioPrimeiroTurno);
                                }
                                elseif($agora > strtotime($fimAdicionalNorturnoManha) && $agoraF < strtotime($inicioPrimeiroTurno)){
                                    // echo "PRE PRIMEIRO TURNO<br>";
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora < strtotime($inicioPrimeiroTurno) && $agoraF > strtotime($inicioPrimeiroTurno)){
                                    // echo "PRE PRIMEIRO - PARCIAL FINAL<br>";
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioPrimeiroTurno);
                                }
                                //POS SEGUNDO TURNO
                                if($agora < strtotime($fimSegundoTurno) && $agoraF > strtotime($fimSegundoTurno)){
                                    // echo "POS SEGUNDO TURNO<br>";
                                    $extras[] = $dataHelper->diferencaDatas($fimSegundoTurno, $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora > strtotime($fimSegundoTurno) && $agoraF < strtotime($inicioAdicionalNorturnoNoite)){
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora < strtotime($inicioAdicionalNorturnoNoite) && $agoraF > strtotime($inicioAdicionalNorturnoNoite)){
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioAdicionalNorturnoNoite);
                                }
                            }
                            else{//NAO POSSUI SEGUNDO TURNO
                                // echo "NAO POSSUI SEG TURNO<br>";
                                if($agora < strtotime($fimAdicionalNorturnoManha) && $agoraF > strtotime($fimAdicionalNorturnoManha)){
                                    // echo "PRE PRIMEIRO TURNO - PARCIAL INICIO<br>";
                                    $extras[] = $dataHelper->diferencaDatas($fimAdicionalNorturnoManha, $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora > strtotime($fimAdicionalNorturnoManha) && $agora < strtotime($inicioPrimeiroTurno) && $agoraF > strtotime($inicioPrimeiroTurno)){
                                    // echo "MAIOR QUE AD NOT MANHA, ENTRA 1 TURNO".$h['ivdataevento']."/".$horas['bilhetes'][$key + 1]['ivdataevento']."<br>";
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioPrimeiroTurno);
                                }
                                elseif($agora > strtotime($fimAdicionalNorturnoManha) && $agoraF < strtotime($inicioPrimeiroTurno)){
                                    // echo "PRE PRIMEIRO TURNO<br>";
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora < strtotime($inicioPrimeiroTurno) && $agoraF > strtotime($inicioPrimeiroTurno)){
                                    // echo "PRE PRIMEIRO - PARCIAL FINAL<br>";
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioPrimeiroTurno);
                                }
                                elseif($agora > strtotime($fimPrimeiroTurno) && $agoraF < strtotime($inicioAdicionalNorturnoNoite)){
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $horas['bilhetes'][$key + 1]['ivdataevento']);
                                }
                                elseif($agora > strtotime($fimPrimeiroTurno) && $agoraF > strtotime($inicioAdicionalNorturnoNoite)){
                                    $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioAdicionalNorturnoNoite);
                                }
                            }
                        }
                    }
                }
                //AJUDANTE
                if($horas['perfil'] == 'A'){
                    if($h['ivmotivotransmissao'] == 10 && $tam < $key){
                        $tipoIntervalo = $dataHelper->verificaDentroIntervalo(
                                                                            $h['ivdataevento'],
                                                                            $horas['bilhetes'][$key + 1]['ivdataevento'],
                                                                            $fimPrimeiroTurno,
                                                                            $inicioSegundoTurno
                                                                            );
                        if($tipoIntervalo == 1){
                            // echo "DENTRO DE INTERVALO<br>";
                            $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $horas['bilhetes'][$key + 1]['ivdataevento']);
                        }
                        elseif($tipoIntervalo == 2){
                            // echo "TODO INTERVALO<br>";
                            $extras[] = $dataHelper->diferencaDatas($fimPrimeiroTurno, $inicioSegundoTurno);
                        }
                        elseif($tipoIntervalo == 3){
                            // echo "PARCIALMENTE INICIO<br>";
                            $extras[] = $dataHelper->diferencaDatas($fimPrimeiroTurno, $horas['bilhetes'][$key + 1]['ivdataevento']);
                        }
                        elseif($tipoIntervalo == 4){
                            // echo "PARCIALMENTE FINAL<br>";
                            $extras[] = $dataHelper->diferencaDatas($h['ivdataevento'], $inicioSegundoTurno);
                        }
                    }
                }
            }
        }
        //JORNADA LIVRE
        if($horas['tipoJornada'] == 'L'){
            $totalTrabalhada = strtotime($horas['data']." ".$totalTrabalhada);
            $totalJornada = strtotime($horas['data']." ".$horas['totalHoras']);
            if($totalTrabalhada > $totalJornada){
                $totalExtra = $totalTrabalhada - $totalJornada;
                return gmdate('H:i:s', $totalExtra);
            }
        }
        //CALCULAR EXTRAS
        $total = 0;
        foreach($extras as $e){
            $total = $total + $e;
        }
        return gmdate('H:i:s', $total);
    }
    //CACULA TEMPO TOTAL DE INTERVALO
    public function calculaIntervalo($horas){
        $dataHelper = new DataHelper;
        $paradasInt = [];
        // echo "CODIGO: ".$horas['codigo']."<br>";
        foreach ($horas['bilhetes'] as $key => $h){
            $tam = count($horas['bilhetes']) - 1;
            if($h['ivmotivotransmissao'] == 10){
                //MOTORISTA  COM AJUDANTE, PARADO EM PONTO, MAS QUE NAO TRABALHA
                if($horas['perfil'] == 'M' && $key < $tam && !is_null($h['ivajudante'])){
                    // echo "ENTROU REGRAS MOTORISTA COM AJUDANTE ".$horas['codigo']."<br>";
                    $paradasInt[] = $h;
                    $paradasInt[] = $horas['bilhetes'][$key + 1];
                }
                //MOTORISTA  SEM AJUDANTE, PARADO EM PONTO, MAS QUE NAO TRABALHA, PONTO EXCESSAO
                if($horas['perfil'] == 'M' && $key < $tam && is_null($h['ivajudante'])){
                    // echo "ENTROU REGRAS MOTORISTA SEM AJUDANTE<br>";
                    $paradasInt[] = $h;
                    $paradasInt[] = $horas['bilhetes'][$key + 1];
                }
                //AJUDANTE
                if($horas['perfil'] == 'A' && is_null($h['ivponto']) && $key < $tam){
                    // echo "ENTROU REGRAS AJUDANTE<br>";
                    $paradasInt[] = $h;
                    $paradasInt[] = $horas['bilhetes'][$key + 1];
                }
            }
        }
        //VERIFICA INTERVALOS
        foreach($paradasInt as $key => $p){
            $inicioIntervalo = $horas['data']." ".$horas['fimPrimeiroTurno'];
            $fimIntervalo    = $horas['data']." ".$horas['inicioSegundoTurno'];
            // dd($horas);
            if(($horas['tipoJornada'] == 'F' && is_null($horas['inicioSegundoTurno'])) || (($horas['tipoJornada'] == 'L' && is_null($horas['totalIntervalo'])))){
                // echo "NAO TEM SEGUNDO TURNO<br>";
                return "00:00:00";
            }
            elseif($horas['tipoJornada'] == 'F'){
                if($p['ivmotivotransmissao'] == 10){
                    $tipoIntervalo = $dataHelper->verificaDentroIntervalo(
                                                                        $p['ivdataevento'],
                                                                        $paradasInt[$key + 1]['ivdataevento'],
                                                                        $inicioIntervalo,
                                                                        $fimIntervalo
                                                                        );
                    if($tipoIntervalo == 0){
                        //remove
                        // echo "FORA DE INTERVALO<br>";
                        unset($paradasInt[$key]);
                        unset($paradasInt[$key + 1]);
                    }
                    elseif($tipoIntervalo == 2){
                        // echo "DENTRO DE INTERVALO<br>";
                        $total = $dataHelper->diferencaDatas($inicioIntervalo, $fimIntervalo);
                        return gmdate('H:i:s', $total);
                    }
                    elseif($tipoIntervalo == 3){
                        // echo "PARCIALMENTE INICIO<br>";
                        $paradasInt[$key]['ivdataevento'] = $inicioIntervalo;
                    }
                    elseif($tipoIntervalo == 4){
                        // echo "PARCIALMENTE FINAL<br>";
                        $paradasInt[$key + 1]['ivdataevento'] = $fimIntervalo;
                    }
                }
            }
        }
        // dd($paradasInt);
        //SOMAR TOTAIS DE INTERVALOS
        $totalIntervaloSegundos = 0;
        foreach($paradasInt as $key => $p){
             if($p['ivmotivotransmissao'] == 10){
                    $totalIntervaloSegundos = $totalIntervaloSegundos + $dataHelper->diferencaDatas($paradasInt[$key]['ivdataevento'], $paradasInt[$key + 1]['ivdataevento']);
                }
        }
        //FIXA
        if($horas['tipoJornada'] == 'F'){
            return gmdate('H:i:s', $totalIntervaloSegundos);
        }
        else{
            $tempoIntervalo = explode(":", $horas['totalIntervalo']);
            $tempoIntervalo = $tempoIntervalo[0] * 3600 + $tempoIntervalo[1] * 60 + $tempoIntervalo[2];
            if($totalIntervaloSegundos <= $tempoIntervalo)
                return gmdate('H:i:s', $totalIntervaloSegundos);
            else
                return gmdate('H:i:s', $tempoIntervalo);
        }

    }
    //CALCULA TOTAL DE HORAS ENTRA PRIMEIRA E ULTIMA IGNICAO DO DIA
    public function calculaTotalHoras($horas){
        $dataHelper = new DataHelper;
        $inicio = $horas['bilhetes'][0]['ivdataevento'];
        $tam = count($horas['bilhetes']) - 1;
        $fim = $horas['bilhetes'][$tam]['ivdataevento'];
        $total = $dataHelper->diferencaDatas($inicio, $fim);
        return gmdate('H:i:s', $total);
    }
    //CALCULA HORA ESPERA
    public function calculaHoraEspera($horas){
        $dataHelper = new DataHelper;
        /*-----------------EXTRAIR BILHETES DE ESPERA------------------------------------*/
        $bilhetesEspera = [];
        //MOTORISTA
        if($horas['perfil'] == 'M'){
            foreach($horas['bilhetes'] as $c => $b){
                $tam = count($horas['bilhetes']) - 1;
                // //SEM AJUDANTE
                if($b['ivmotivotransmissao'] == 10 && is_null($b['pontoexcessao']) && $c < $tam && is_null($b['ivajudante']) && $horas['regraMotoristaSemAjudante'] == 'E'){
                    // echo "ENTROU SEM AJUDANTE<br>";
                    if($horas['tipoJornada'] == 'F'){
                        $bilhetesEspera[] = $b;
                        $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                    }
                    else{
                        if(!is_null($b['ivponto']) && ($b['potipo'] == 'E' || $b['potipo'] == 'C')){
                            $bilhetesEspera[] = $b;
                            $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                        }
                    }
                }
                //COM AJUDANTE
                if($b['ivmotivotransmissao'] == 10 && $c < $tam && !is_null($b['ivajudante']) && $horas['regraMotoristaComAjudante'] == 'E'){
                    // echo "ENTROU COM AJUDANTE<br>";
                    if($horas['tipoJornada'] == 'F'){
                        $bilhetesEspera[] = $b;
                        $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                    }
                    else{
                        if(!is_null($b['ivponto']) && ($b['potipo'] == 'E' || $b['potipo'] == 'C')){
                            $bilhetesEspera[] = $b;
                            $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                        }
                    }
                }
                //SEM AJUDANTE EM PONTO DE EXCESSAO
                if($b['ivmotivotransmissao'] == 10 && !is_null($b['pontoexcessao']) && $c < $tam && is_null($b['ivajudante']) && $horas['regraMotoristaComAjudante'] == 'T'){
                    // echo "SEM AJUDANTE EM PONTO DE EXCESSAO<br>";
                    if($horas['tipoJornada'] == 'F'){
                        $bilhetesEspera[] = $b;
                        $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                    }
                    else{
                        if(!is_null($b['ivponto']) && ($b['potipo'] == 'E' || $b['potipo'] == 'C')){
                            $bilhetesEspera[] = $b;
                            $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                        }
                    }
                }
                //SEM AJUDANTE FORA DE PONTO
                if($horas['tipoJornada'] == 'F' && $b['ivmotivotransmissao'] == 10 && $c < $tam && is_null($b['ivponto']) && $horas['regraMotoristaSemAjudante'] == 'T' && is_null($b['ivajudante'])){
                    $bilhetesEspera[] = $b;
                    $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                }
            }
        }
        //AJUDANTE
        if($horas['perfil'] == 'A'){
            foreach($horas['bilhetes'] as $c => $b){
                if($b['ivmotivotransmissao'] == 9 && $horas['regraAjudante'] == 'E'){
                    if($horas['tipoJornada'] == 'F'){
                        $bilhetesEspera[] = $b;
                        $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                    }
                    else{
                        if(!is_null($b['ivponto']) && ($b['potipo'] == 'E' || $b['potipo'] == 'C')){
                            $bilhetesEspera[] = $b;
                            $bilhetesEspera[] = $horas['bilhetes'][$c + 1];
                        }
                    }
                }
            }
        }
        //----------FIXA---------------------------------------------------------------
        $chavesRemover = [];//guarda chaves para remover após laço
        if($horas['tipoJornada'] == 'F'){
            $inicioIntervalo     = $horas['data']." ".$horas['fimPrimeiroTurno'];
            $fimIntervalo        = $horas['data']." ".$horas['inicioSegundoTurno'];
            $inicioPrimeiroTurno = $horas['data']." ".$horas['inicioPrimeiroTurno'];
            $fimPrimeiroTurno    = $horas['data']." ".$horas['fimPrimeiroTurno'];
            $inicioSegundoTurno  = $horas['data']." ".$horas['inicioSegundoTurno'];
            $fimSegundoTurno     = $horas['data']." ".$horas['fimSegundoTurno'];
            // echo "ENTROU FIXA<br>";
            //VERIFICA SE ESPERAR ESTAO DENTRO DE UM INTERVALO
            $tam = count($bilhetesEspera) - 1;
            foreach ($bilhetesEspera as $key => $p) {
                if($horas['perfil'] == 'M'){
                    //se tiver segundo turno
                    if(!is_null($horas['inicioSegundoTurno'])){
                        // echo "TEM SEGUNDO TURNO<br>";
                        if($p['ivmotivotransmissao'] == 10){
                            //PRIMEIRO TURNO
                            $primeiroTurno = $dataHelper->verificaDentroIntervalo(
                                                                                $p['ivdataevento'],
                                                                                $bilhetesEspera[$key + 1]['ivdataevento'],
                                                                                $inicioPrimeiroTurno,
                                                                                $fimPrimeiroTurno
                                                                                );
                            $segundoTurno  = $dataHelper->verificaDentroIntervalo(
                                                                                $p['ivdataevento'],
                                                                                $bilhetesEspera[$key + 1]['ivdataevento'],
                                                                                $inicioSegundoTurno,
                                                                                $fimSegundoTurno
                                                                                );
                            if($primeiroTurno == 0 && $segundoTurno == 0){
                                $chavesRemover[] = $key;
                                $chavesRemover[] = $key + 1;
                            }
                            elseif($primeiroTurno == 2 || $segundoTurno == 2){
                                if($primeiroTurno == 2){
                                    // echo "TODO PRIMEIRO TURNO<br>";
                                    $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                                    $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                                }
                                else{
                                    // echo "TODO SEGUNDO TURNO<br>";
                                    $bilhetesEspera[$key]['ivdataevento'] = $inicioSegundoTurno;
                                    $bilhetesEspera[$key + 1]['ivdataevento'] = $fimSegundoTurno;
                                }
                            }
                            elseif($primeiroTurno == 3 || $segundoTurno == 3){
                                // echo "INICIO TURNO<br>";
                                if($primeiroTurno == 3)
                                    $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                                else
                                    $bilhetesEspera[$key]['ivdataevento'] = $inicioSegundoTurno;
                            }
                            elseif($primeiroTurno == 4 || $segundoTurno == 4){
                                // echo "FIM TURNO<br>";
                                if($primeiroTurno == 4)
                                    $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                                else
                                    $bilhetesEspera[$key + 1]['ivdataevento'] = $fimSegundoTurno;
                            }
                        }
                    }
                    else{//SEM INTERVALO
                        if($p['ivmotivotransmissao'] == 10){
                            //PRIMEIRO TURNO
                            $primeiroTurno = $dataHelper->verificaDentroIntervalo(
                                                                                $p['ivdataevento'],
                                                                                $bilhetesEspera[$key + 1]['ivdataevento'],
                                                                                $inicioPrimeiroTurno,
                                                                                $fimPrimeiroTurno
                                                                                );
                            if($primeiroTurno == 0){
                                $chavesRemover[] = $key;
                                $chavesRemover[] = $key + 1;
                            }
                            elseif($primeiroTurno == 3){
                                $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                            }
                            elseif($primeiroTurno == 4){
                                $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                            }
                            elseif($primeiroTurno == 2){
                                $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                                $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                            }
                        }
                    }
                }
                if($horas['perfil'] == 'A'){
                    if(!is_null($horas['inicioSegundoTurno'])){
                        if($p['ivmotivotransmissao'] == 9 && $tam < $key){
                            //PRIMEIRO TURNO
                            $tipoIntervalo = $dataHelper->verificaDentroIntervalo(
                                                                                $p['ivdataevento'],
                                                                                $bilhetesEspera[$key + 1]['ivdataevento'],
                                                                                $inicioPrimeiroTurno,
                                                                                $fimPrimeiroTurno
                                                                                );
                            if($tipoIntervalo == 0){
                                $chavesRemover[] = $key;
                                $chavesRemover[] = $key + 1;
                            }
                            elseif($tipoIntervalo == 3){
                                $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                            }
                            elseif($tipoIntervalo == 4){
                                $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                            }
                            elseif($tipoIntervalo == 2){
                                $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                                $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                            }
                        }
                    }
                    else{//SEM INTERVALO
                        if($p['ivmotivotransmissao'] == 9){
                            //PRIMEIRO TURNO
                            $tipoIntervalo = $dataHelper->verificaDentroIntervalo(
                                                                                $p['ivdataevento'],
                                                                                $bilhetesEspera[$key + 1]['ivdataevento'],
                                                                                $inicioPrimeiroTurno,
                                                                                $fimPrimeiroTurno
                                                                                );
                            if($tipoIntervalo == 0){
                                $chavesRemover[] = $key;
                                $chavesRemover[] = $key + 1;
                            }
                            elseif($tipoIntervalo == 3){
                                $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                            }
                            elseif($tipoIntervalo == 4){
                                $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                            }
                            elseif($tipoIntervalo == 2){
                                $bilhetesEspera[$key]['ivdataevento'] = $inicioPrimeiroTurno;
                                $bilhetesEspera[$key + 1]['ivdataevento'] = $fimPrimeiroTurno;
                            }
                        }
                    }
                }
            }//fim foreach
        }
        $totalEsperaSegundos = 0;
        //-----------------------------------------------------------------
        //-------------REMOVER HORARIOS INVALIDOS--------------------------
        //-----------------------------------------------------------------
        foreach ($chavesRemover as $key => $c){
            unset($bilhetesEspera[$c]);
        }
        //-----------------------------------------------------------------
        //------------------CALCULAR TOTAL DE HORAS ESPERA-----------------
        //-----------------------------------------------------------------
        if($horas['perfil'] == 'M'){
            foreach ($bilhetesEspera as $key => $p) {
                if($p['ivmotivotransmissao'] == 10){
                    $totalEsperaSegundos = $totalEsperaSegundos + $dataHelper->diferencaDatas($bilhetesEspera[$key]['ivdataevento'], $bilhetesEspera[$key + 1]['ivdataevento']);
                }
            }
        }
        else{
            foreach ($bilhetesEspera as $key => $p) {
                if($p['ivmotivotransmissao'] == 9){
                    $totalEsperaSegundos = $totalEsperaSegundos + $dataHelper->diferencaDatas($bilhetesEspera[$key]['ivdataevento'], $bilhetesEspera[$key + 1]['ivdataevento']);
                }
            }
        }
        return gmdate('H:i:s', $totalEsperaSegundos);
    }
    //------------------------------------------------------------------
    //-------------CALCULA HORA TRABALHADA------------------------------
    //------------------------------------------------------------------
    public function calculaHoraTrabalhada($horas)
    {
        $dataHelper = new DataHelper;
        $bilhetes = $horas->bilhetes;
        $trabalhou = [];
        $ifDataFinal = true;

        foreach ($bilhetes as $b => $bilhete) {
            switch ($bilhete['tipo']) {
                case 'M': {
                    //Inicio de jornada
                    if ($bilhete['ivmotivotransmissao'] == 9) {
                        if ($ifDataFinal)
                            $dataInicio = $bilhete['ivdataevento'];
                        continue;
                    }
                    //FIM JORNADA
                    if (count($bilhetes)-1 == $b) {
                        $trabalhou[] = $dataHelper->diffDatas($dataInicio, $bilhete['ivdataevento'], '%H:%I:%S');
                        continue;
                    }

                    if (isset($bilhete['ivajudante'])) {
                        if (isset($bilhete['ivponto']) && $horas['regraMotoristaComAjudante'] == 'T') {
                            $ifDataFinal = false;
                        } else {
                            $trabalhou[] = $dataHelper->diffDatas($dataInicio, $bilhete['ivdataevento'], '%H:%I:%S');
                            $ifDataFinal = true;
                        }
                    } else {
                        if (isset($bilhete['ivponto']) && $horas['regraMotoristaSemAjudante'] == 'T' && !$bilhete['pontoexcessao']) {
                            $ifDataFinal = false;
                        } else {
                            $trabalhou[] = $dataHelper->diffDatas($dataInicio, $bilhete['ivdataevento'], '%H:%I:%S');
                            $ifDataFinal = true;
                        }
                    }
                    break;
                }
                default: {
                    if (count($bilhetes)-1 == $b) continue;

                    //inicia a contagem
                    if ($bilhete['ivmotivotransmissao'] == 9 && isset($dataInicio)) {
                        $trabalhou[] = $dataHelper->diffDatas($dataInicio, $bilhete['ivdataevento'], '%H:%I:%S');
                        continue;
                    }
                    $dataInicio = null;
                    //Finaliza
                    if ($bilhete['ivmotivotransmissao'] == 10 && ($bilhete['potipo'] == 'E' || $bilhete['potipo'] == 'C')) {
                        $dataInicio = $bilhete['ivdataevento'];
                    }

                    break;
                }
            }
        }

        return $dataHelper->somaHora($trabalhou);
    }
    public function verificaFeriado($data)
    {
        $dia = new DateTime($data['bilhetes'][0]['ivdataevento']);
        $cliente = $data['bilhetes'][0]['ivcliente'];

        $feriados = Feriado::where('frdata', '1900-'.$dia->format('m-d'))
            ->where(function($query) use ($cliente){
                $query->where('frcliente', '=', $cliente)
                    ->orWhere('frcliente', '=', 1);
            })->get();
        $feriado = null;
        if(!$feriados->isEmpty())
            $feriado = "S";

        return $feriado;
    }

    public function calculaHoraFalta($horas, $horasTrabalhadas = null)
    {
        $dataHelper = new DataHelper;
        $bilhetes = $horas->bilhetes;
        $diferenca = [];
        $ifPrimeiroTurno = true;

        switch ($horas['tipoJornada']) {
            case 'F': {
                foreach ($bilhetes as $b => $bilhete) {
                    $horaJornada = date('H:i:s', strtotime($bilhete['ivdataevento']));

                    //ultimo horario
                    if (count($bilhetes)-1 == $b) {
                        if ($horaJornada < $horas['fimPrimeiroTurno'] && $horaJornada > $horas['inicioPrimeiroTurno']) {
                            $diferenca[] = $dataHelper->diffDatas($horaJornada, $horas['fimPrimeiroTurno'], '%H:%I:%S');
                            if (isset($horas['inicioSegundoTurno'])) {
                                $diferenca[] = $dataHelper->diffDatas($horas['inicioSegundoTurno'], $horas['fimSegundoTurno'], '%H:%I:%S');
                            }
                            continue;
                        } elseif($horaJornada < $horas['fimPrimeiroTurno'] && $horaJornada < $horas['inicioPrimeiroTurno']) {
                            $diferenca[] = $dataHelper->diffDatas($horas['inicioPrimeiroTurno'], $horas['fimPrimeiroTurno'], '%H:%I:%S');
                        }

                        if (isset($horas['inicioSegundoTurno']) && $horaJornada > $horas['inicioSegundoTurno'] && $horaJornada < $horas['fimSegundoTurno']) {
                            $diferenca[] = $dataHelper->diffDatas($horaJornada, $horas['fimSegundoTurno'], '%H:%I:%S');
                            continue;
                        }


                        if (isset($horas['inicioSegundoTurno']) && $horaJornada < $horas['inicioSegundoTurno'] && $horaJornada < $horas['fimSegundoTurno']) {
                            $diferenca[] = $dataHelper->diffDatas($horas['inicioSegundoTurno'], $horas['fimSegundoTurno'], '%H:%I:%S');
                            continue;
                        }

                        if (isset($horas['inicioSegundoTurno']) && $horaJornada > $horas['fimSegundoTurno'])
                            $diferenca[] = $dataHelper->diffDatas($horas['inicioSegundoTurno'], $horas['fimSegundoTurno'], '%H:%I:%S');
                        continue;

                    }

                    //Verifica se o primeiro horario é hora falta
                    if ($bilhete['ivmotivotransmissao'] == 9 && $horaJornada > $horas['inicioPrimeiroTurno'] && $b == 0) {
                        if (isset($horas['inicioSegundoTurno']) && $horaJornada > $horas['inicioSegundoTurno']) {
                            $diferenca[] = $dataHelper->diffDatas($horas['fimPrimeiroTurno'], $horas['inicioPrimeiroTurno'], '%H:%I:%S');
                        } elseif(isset($horas['inicioSegundoTurno']) && $horaJornada > $horas['inicioPrimeiroTurno'] && $horaJornada < $horas['fimPrimeiroTurno']) {
                            $diferenca[] = $dataHelper->diffDatas($horaJornada, $horas['inicioPrimeiroTurno'], '%H:%I:%S');
                        } else {
                            $diferenca[] = $dataHelper->diffDatas($horas['inicioPrimeiroTurno'], $horas['fimPrimeiroTurno'], '%H:%I:%S');
                        }

                        continue;
                    }

                    //Verifica se existe o proximo horario, se nao, força um true
                    if (isset($bilhetes[$b+1]['ivdataevento']))
                        $proxHoraJornada = date('H:i:s', strtotime($bilhetes[$b+1]['ivdataevento']));
                    else
                        $proxHoraJornada = '23:59:59';

                    if ($bilhete['ivmotivotransmissao'] == 10 && ($bilhete['potipo'] != 'E' || $bilhete['potipo'] != 'C') && $horaJornada < $horas['fimPrimeiroTurno'] && $proxHoraJornada > $horas['fimPrimeiroTurno']) {
                        $diferenca[] = $dataHelper->diffDatas($horaJornada, $horas['fimPrimeiroTurno'], '%H:%I:%S');
                        continue;
                    }

                    //Verifica se existe o proximo horario, se nao, força um true
                    if (isset($bilhetes[$b-1]['ivdataevento']))
                        $antHoraJornada = date('H:i:s', strtotime($bilhetes[$b-1]['ivdataevento']));
                    else
                        $antHoraJornada = '00:00:00';

                    if (isset($horas['inicioSegundoTurno']) && $bilhete['ivmotivotransmissao'] == 9 &&
                        $horaJornada > $horas['inicioSegundoTurno'] && $antHoraJornada < $horas['inicioSegundoTurno'] &&
                        $horaJornada < $horas['fimSegundoTurno']) {
                        $diferenca[] = $dataHelper->diffDatas($horaJornada, $horas['inicioSegundoTurno'], '%H:%I:%S');
                    }
                }

                break;
            }
            case 'L': {
                if (isset($horasTrabalhadas)) {
                    if ($horasTrabalhadas < $horas['totalHoras']) {
                        return $dataHelper->diffDatas($horasTrabalhadas, $horas['totalHoras'], '%H:%I:%S');
                    } else {
                        return '00:00:00';
                    }
                } else {
                    return $horas['totalHoras'];
                }
                break;
            }
            default:
                return 'Jornada não encontrada!';
                break;
        }

        return $dataHelper->somaHora($diferenca);
    }

    public function extraNoturno($horas)
    {
        $dataHelper = new DataHelper;
        $horarios = $horas->bilhetes;
        $extraNot = ['00:00:00'];
        foreach ($horarios as $i => $horario) {

            // if($horario['codigo'] != 25) continue;
            $dataevento = new DateTime($horario['ivdataevento']);
            $data = $dataevento->format('Y/m/d');
            $inicioAdd = new DateTime($data.' 05:00:00');
            $fimAdd = new DateTime($data.' 22:00:00');
            $inicioManha = new DateTime($data.' '.$horas['fimSegundoTurno']);
            $fimTarde = new DateTime($data.' '.$horas['fimSegundoTurno']);
            $fimTarde = new DateTime($data. ' 22:00:00');

            if($horario['tipo'] == "M"){
                if(isset($horario['ivajudante'])){
                    $mtAju = $horas['regraMotoristaComAjudante'] == "T";
                }else{
                    $mtAju = $horas['regraMotoristaSemAjudante'] == "T";
                }
                $excessao = isset($horario['pontoexcessao']) && $horario['pontoexcessao'] != $horario['ivponto'];

                if($inicioManha > $dataevento && $horas['tipoJornada'] == 'F' && $mtAju && !$excessao && isset($horarios[$i + 1]) && $dataevento < $inicioAdd && (isset($horario['ivponto']) || $horario['ivmotivotransmissao'] == 9)) {
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioManha){
                        $parou = $inicioManha;
                    }

                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }elseif($inicioManha > $dataevento && $horas['tipoJornada'] == "F" && !$mtAju && $horario['ivmotivotransmissao'] == 9 && $dataevento < $inicioAdd){
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioManha){
                        $parou = $inicioManha;
                    }

                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }elseif($fimTarde < $dataevento && $mtAju && !$excessao && isset($horarios[$i - 1]) && $dataevento > $fimAdd && (isset($horario['ivponto']) || $horario['ivmotivotransmissao'] == 10)) {
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimTarde){
                        $parou = $fimTarde;
                    }

                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');

                }elseif($fimTarde < $dataevento && !$mtAju && $horario['ivmotivotransmissao'] == 10  && $dataevento > $fimAdd && isset($horarios[$i - 1])){
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimTarde){
                        $parou = $fimTarde;
                    }

                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }
            }elseif($horario['tipo'] == 'A'){
                $ajuPonto = isset($horarios[$i + 1]) && $horario['ivmotivotransmissao'] == 9 && isset($horario['ivponto']);
                if($inicioManha > $dataevento && $horas['regraAjudante'] == "T" && $horas['tipoJornada'] == "F" &&  $dataevento < $inicioAdd && isset($horario['ivponto'])) {
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioManha){
                        $parou = $inicioManha;
                    }


                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');

                }elseif($inicioManha > $dataevento && $horas['regraAjudante'] == "E" && $horas['tipoJornada'] == "F" && $dataevento < $inicioAdd && $ajuPonto) {
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioManha){
                        $parou = $inicioManha;
                    }


                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');

                }elseif($fimTarde < $dataevento && $horas['regraAjudante'] == "T" && $dataevento > $fimAdd && isset($horario['ivponto'])) {
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimTarde){
                        $parou = $fimTarde;
                    }


                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }elseif($fimTarde < $dataevento && $horas['regraAjudante'] == "E" && $dataevento > $fimAdd && $horario['ivmotivotransmissao'] == 10 && isset($horario['ivponto'])) {
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimTarde){
                        $parou = $fimTarde;
                    }


                    $extraNot[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }
            }
        }
        $estraNotTotal = $dataHelper->somaHora($extraNot);
        return $estraNotTotal;
    }

    public function adicNoturno($horas)
    {
        $dataHelper = new DataHelper;
        $horarios = $horas->bilhetes;
        $adicional = ['00:00:00'];
        foreach ($horarios as $i => $horario) {
            // if($horario['codigo'] != 25) continue;
            $dataevento = new DateTime($horario['ivdataevento']);
            $data = $dataevento->format('Y/m/d');
            $inicioAdd = new DateTime($data.' 05:00:00');
            $fimAdd = new DateTime($data.' 22:00:00');
            if($horario['tipo'] == "M"){
                if(isset($horario['ivajudante'])){
                    $mtAju = $horas['regraMotoristaComAjudante'] == "T";
                }else{
                    $mtAju = $horas['regraMotoristaSemAjudante'] == "T";
                }
                $excessao = isset($horario['pontoexcessao']) && $horario['pontoexcessao'] != $horario['ivponto'];

                if($horas['tipoJornada'] == 'F' && $mtAju && !$excessao && isset($horarios[$i + 1]) && $dataevento < $inicioAdd && (isset($horario['ivponto']) || $horario['ivmotivotransmissao'] == 9)){
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioAdd){
                        $parou = $inicioAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }elseif($horas['tipoJornada'] == "F" && !$mtAju && $horario['ivmotivotransmissao'] == 9 && $dataevento < $inicioAdd){
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioAdd){
                        $parou = $inicioAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }elseif($mtAju && !$excessao && isset($horarios[$i - 1]) && $dataevento > $fimAdd && (isset($horario['ivponto']) || $horario['ivmotivotransmissao'] == 10)) {
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimAdd){
                        $parou = $fimAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');

                }elseif(!$mtAju && $horario['ivmotivotransmissao'] == 10 && $dataevento > $fimAdd && isset($horarios[$i - 1])){
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimAdd){
                        $parou = $fimAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }
            }elseif($horario['tipo'] == 'A'){
                $ajuPonto = isset($horarios[$i + 1]) && $horario['ivmotivotransmissao'] == 9 && isset($horario['ivponto']);
                if($horas['regraAjudante'] == "T" && $horas['tipoJornada'] == "F" &&  $dataevento < $inicioAdd && isset($horario['ivponto'])) {
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioAdd){
                        $parou = $inicioAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');

                }elseif($horas['regraAjudante'] == "E" && $horas['tipoJornada'] == "F" && $dataevento < $inicioAdd && $ajuPonto) {
                    $parou = new DateTime($horarios[$i + 1]['ivdataevento']);
                    if($parou > $inicioAdd){
                        $parou = $inicioAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');

                }elseif($horas['regraAjudante'] == "T" && $dataevento > $fimAdd && isset($horario['ivponto'])) {
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimAdd){
                        $parou = $fimAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }elseif($horas['regraAjudante'] == "E" && $dataevento > $fimAdd && $horario['ivmotivotransmissao'] == 10 && isset($horario['ivponto'])) {
                    $parou = new DateTime($horarios[$i - 1]['ivdataevento']);
                    if($parou < $fimAdd){
                        $parou = $fimAdd;
                    }

                    $adicional[] = $dataevento->diff($parou)->format('%H:%I:%S');
                }
            }
        }

        $adicionalTotal = $dataHelper->somaHora($adicional);
        return $adicionalTotal;
    }
}

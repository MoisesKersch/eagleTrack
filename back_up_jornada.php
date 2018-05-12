<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Motorista;
use App\Models\Bilhete;
use App\Models\Cliente;
use App\Models\FechamentoFolha;
use App\Models\Veiculo;
use DB;
use Excel;
use App\Helpers\DataHelper;
use App\Helpers\PdfHelper;

class JornadaTrabalhoController extends Controller
{
    public function listar()
    {
        if(\Auth::user()->usumaster == 'S') {
            $clientes = Cliente::select('clcodigo', 'clnome')->get();
        }else{
            $clientes = \Auth::user()->clientes;
        }
        return view('relatorios.jornadaTrabalho.listar', compact('clientes'));
    }
    public function relatorio(Request $request)
    {
        //$motoristas = '';
        //if(!$request->buscar)
        // $motoristas = Motorista::whereIn('mtcliente', $request->id)->get();
        $jornada = $this->query($request);
        return response([
            'jornada' => $jornada,
            // 'motoristas' => $motoristas,
            'usuario' => \Auth::user()->name,
        ]);
    }
    public function todos(Request $request)
    {
        $idCli = Veiculo::select('vecodigo')
            ->join('clientes', 'clcodigo', '=', 'veproprietario')
            ->join('usuarios_clientes', 'clcodigo', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)->get();

        $id = '';
        foreach($idCli as $i){
            $id .= $i->vecodigo.',';
        }
        $request->buscar = trim($id, ',');

        $dados = $this->query($request);

        $clientes = Cliente::with('motoristas')
            ->join('usuarios_clientes', 'clcodigo', '=', 'uclcliente')
            ->where('uclusuario', '=', \Auth::user()->id)
            ->get();

        return response ([
            'dados' => $dados,
            'clientes' => $clientes,
        ]);
    }
    public function exportar(Request $request)
    {
        $jornada = $request->dados;
        $type = $request->type;
        $th = 'Data, Identificação, Semana, Trabalhada, Falta, Extra, Extra 100%, Ad.Noturno, Extra Noturno, Hora Espera, Int.Refeição';
        if($type == 'pdf') {
            $pdf = new PdfHelper;
            $nome = 'Jornada de trabalho';
            $nome = $pdf->geraPdf($request, $jornada, $th, $nome);
            return response ([
                'dados' => $nome,
            ]);
        }else{
            $jornada = explode(';',  $jornada);
            $jorn = [];
            foreach($jornada as $i => $v) {
                $dado = trim($v, '*i&');
                $jorn[$i] = explode('*i&', $dado);
            }

            return Excel::create('Jornada_trabalho', function($excel) use ($jorn){
                $excel->sheet('mySheet', function($sheet) use ($jorn)
                {
                    $sheet->fromArray($jorn);
                    $sheet->row(1, array(
                        'Data', 'Identificação', 'Semana', 'Trabalhada', 'Falta', 'Extra', 'Extra 100%', 'Ad.Noturno', 'Extra Noturno', 'Hora Espera', 'Int.Refeição'
                    ));
                });
            })->download($type);
        }
    }
    public function query($request)
    {
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        if(gettype($request->buscar) == 'array'){
            $id = implode(", ", $request->buscar);
        }else {
            $id = $request->buscar;
        }
        // if(empty($id)) {
        //     $mts = Motorista::whereIn('mtcliente', $request->clientes)->get();
        //     $id = '';
        //     foreach($mts as $mt) {
        //         $id .= $mt->mtcodigo.',';
        //     }
        //     $id = trim($id, ',');
        // }
        if(empty($id)) {
            return $trabalho = '';
        }
        $dataInicio = $request->data_inicio.' 00:00:00';
        $dataFim = $request->data_fim. ' 23:59:49';

        $trabalho = DB::table('fechamento_folhas')
            ->select('fedataentrada as afedataentrada', 'mtnome as bmtnome',
            'fehoratrabalhada as dfehoratrabalhada', 'feextranoturno as ifeextranoturno',
            'fehorafalta as efehorafalta', 'fehoraextra as ffehoraextra',
            'fehoracemcento as gfehoracemcento', 'fehoranoturna as hfehoranoturna',
            'fehoraespera as jfehoraespera', 'feintervalo as kfeintervalo')
            ->where('fedataentrada', '>', $dataInicio)
            ->join('motoristas', 'mtcodigo', '=', 'femotorista')
            ->where('fedataentrada', '<', $dataFim)
            ->whereRaw('femotorista in ('.$id.')')
            ->get();
        foreach($trabalho as $i => $trab) {
            $entrada = new \DateTime($trab->afedataentrada);
            $trabalho[$i]->afedataentrada = $entrada->format('d/m/Y H:i:s');
            $trabalho[$i]->csemana = strftime('%A', strtotime($entrada->format('Y-m-d')));
            $trabalho[$i]->gfehoracemcento = $trab->gfehoracemcento ? : '00:00:00';
            $trabalho[$i]->hfehoranoturna = $trab->hfehoranoturna ? : '00:00:00';
            $array = (array)$trabalho[$i];
            ksort($array);
            $trabalho[$i] = $array;
        }
        return $trabalho;
    }
    public function script(Request $request)
    {
        $dia = $request->d;
        $dataHelper = new DataHelper;
        $dia = new \Datetime($dia ? : '');
        $data = date('Y/m/d', strtotime('-1 days', strtotime($dia->format('Y/m/d'))));
        // date('w', strtotime($inicio)) == 6
        $nunDia = date('w', strtotime($dia->format('Y-m-d')));
        $final= $dia->format('Y/m/d').' 00:00:00';
        $inicio = $data.' 00:00:00';
        // dd($final, $data, $dia, $inicio);
        $ids = DB::select("select distinct on (bimotorista) bimotorista from bilhetes where bimotorista > 0");
        $strId = 0;
        foreach($ids as $i => $id) {
            $strId .= ','.(integer) $id->bimotorista;
            $comeco = Bilhete::select('bimotorista', DB::raw('min(bidataevento) as bidataevento'), 'biplaca', 'hj.*')
                ->join('motoristas', 'mtcodigo', '=', 'bimotorista')
                ->join('jornada_trabalho', 'mtjornada', '=', 'jtcodigo')
                ->join('horas_jornada_trabalho as hj', 'jtcodigo', 'hjtjornada')
                ->where('bimotorista', '=', $id->bimotorista)
                ->where('hjtdiasemana', '=', $nunDia)
                ->where('bidataevento', '>', $inicio)
                ->where('bidataevento', '<', $final)
                ->where('biignicao', '=', 1)
                ->groupBy('bimotorista', 'biplaca', 'mtnome', 'hj.hjtcodigo')
                ->orderBy('bimotorista')
                ->first();
            if(!empty($comeco)) {
                $horario[$id->bimotorista] = $comeco;
            }
        }
        if(!isset($horario)) return "nada consta!";
        $ligaTarde = DB::select("select bimotorista,
            max(bidataevento) bidataevento
            from bilhetes
            where bimotorista > 0
            and bidataevento > '$inicio'
            and bidataevento < '$final'
            and biignicao = 1
            group by bimotorista
            order by bimotorista");
        $hfinal = [];
        foreach($ligaTarde as $liga) {
            $ligouTarde[$liga->bimotorista] = $liga;
            $fHora = $liga->bidataevento;
            $id = $liga->bimotorista;
            $finalHora = Bilhete::select('bidataevento', 'bimotorista')
                ->where('bidataevento', '>=', $fHora)
                ->where('biignicao', '=', 0)
                ->where('bimotorista', '=', $id)
                ->orderBy('bidataevento', 'asc')
                ->first();

            if(empty($finalHora->bidataevento) || !isset($horario[$liga->bimotorista])) continue;
            $hfinal[$liga->bimotorista] = $finalHora;
            $hcomeco = $horario[$liga->bimotorista]->bidataevento;
            // $f = new DateTime($final[$liga['bimotorista']]['bidataevento']);
            $hfim = $finalHora->bidataevento;
            $hespera = DB::select("select bilatlog, bidataevento,
                    biignicao, bicodigo, bimotorista
                    from bilhetes where bilatlog in (
                        select distinct bilatlog
                        from bilhetes
                        where bimotorista = '$id'
                        and biignicao = 0
                        and bidataevento > '$hcomeco'
                        and bidataevento < '$hfim'
                        group by bilatlog having
                        count(*) > 1 order by bilatlog asc)
					and bimotorista = '$id'
					and bidataevento > '$hcomeco'
					and bidataevento < '$hfim'
					and biignicao = 0
                    order by bidataevento asc");
            if(empty($hespera)) continue;

            $latlog = '';
            $count = 0;
            $log = [];
            foreach($hespera as $espera) {
                if($espera->bilatlog == $latlog) {
                    $log[$count][] = $espera->bidataevento;
                    $acum[$count] = $latlog;
                }else {
                    $count ++;
                    $log[$count][] = $espera->bidataevento;
                }
                $latlog = $espera->bilatlog;
            }
            foreach($log as $k => $lat) {
			    array_multisort($lat);
                $parou = new \DateTime(array_shift($lat));
                $foi = new \Datetime(array_pop($lat));
                $parado[$k] = $parou->diff($foi)->format('%H:%I:%S');
            }
            $horasParado[$espera->bimotorista] = $dataHelper->somaHora($parado);
        }
        $chave ='';
        foreach ($horario as $k => $hor) {
            if(!empty($hor->hjtinisegundot)){
                $intervalo = Bilhete::select('bidataevento', 'bimotorista')
                    ->where('bidataevento', '>', $data.' '.$hor->hjtfimprimeirot)
                    ->where('bidataevento', '<', $data.' '.$hor->hjtinisegundot)
                    ->where('bimotorista', '=', $hor->bimotorista)
                    ->where('bimotivotransmissao', '=', 9)
                    ->first();
                if(empty($intervalo)) continue;
                $fintervalo = Bilhete::select('bidataevento')
                    ->where('bidataevento', '>', $intervalo->bidataevento)
                    ->where('bidataevento', '<', $data.' '.$hor->hjtinisegundot)
                    ->where('bimotorista', '=', $intervalo->bimotorista)
                    ->first();
                if(empty($fintervalo)) $fintervalo = $data.' '.$hor->hjtinisegundot;
            $chave .= $k.',';
            }
        }
        dd($intervalo);
        $metade = Bilhete::select('bicodigo', 'bidataevento', 'bimotorista', 'biignicao')
            ->where('bidataevento', '>', $data.' 11:00:00')
            ->where('bidataevento', '<', $data.' 14:00:00')
            ->whereRaw("bimotorista in (".trim($chave, ',').")")
            ->orderBy('bimotorista', 'asc')
            ->orderBy('bidataevento', 'asc')
            ->get();
            // dd($metade);

        $colab = '';
        if($metade->isEmpty()) return 'Nada Consta!';
        foreach($metade as $g => $met) {
            $ini = $horario[$met->bimotorista]->bidataevento;
            if($ini == null) continue;
            $idCol = $met->bimotorista;
            if($colab != $idCol) {
                if($met->biignicao == 0) {
                    $horaFim = $met->bidataevento;
                    $andando = Bilhete::select('bicodigo', 'bidataevento', 'bimotorista', 'biignicao')
                        ->whereRaw("bidataevento >= (select bidataevento
                                    from bilhetes
                                    where bidataevento < '$horaFim'
                                    and biignicao = 1
                                    and bimotorista = $idCol
                                    ORDER BY bidataevento desc
                                    limit 1)")
                        ->where('biignicao', '=', 0)
                        ->where('bimotorista', '=', $idCol)
                        ->where('bidataevento', '>', $ini)
                        ->orderBy('bidataevento', 'asc')
                        ->first();
                    if(empty($andando)) continue;
                    $iniInter = new \DateTime($andando->bidataevento);
                    $naoParo = $andando->bidataevento;
                    // var_dump($naoParo, $final);
                    $fimIntervalo = Bilhete::select('bicodigo', 'bidataevento', 'bimotorista', 'biignicao')
                        ->where('bidataevento', '>', $naoParo)
                        ->where('bidataevento', '<', $final)
                        ->where('biignicao', '=', 1)
                        ->where('bimotorista', '=', $idCol)
                        ->first();
                        // $fimIntervalo->bidataevento = $final;
                    // var_dump($final);
                    // var_dump($fimIntervalo->bidataevento);

                    if(!empty($fimIntervalo)){
                        $fimInter = new \DateTime($fimIntervalo->bidataevento);
                    }else{
                        $fimInter = new \DateTime($final);
                    }
                    if(new \DateTime($iniInter->diff($fimInter)->format('%H:%I:%S')) > new \DateTime('00:30:00')) {
                        $ligado[$idCol] = $andando;
                        $colab = $idCol;
                    }
                    $colNun[$idCol] = $idCol;
                    $colCod[$idCol] = $met->bicodigo;
                    $colHora[$idCol] = $met->bidataevento;
                    $colStatus[$idCol] = $met->biignicao;
                }else {
                    $colNun[$idCol] = $idCol;
                    $colCod[$idCol] = $met->bicodigo;
                    $colHora[$idCol] = $met->bidataevento;
                    $colStatus[$idCol] = $met->biignicao;
                }
            }else {
                    $colNun[$idCol] = $idCol;
                    $colCod[$idCol] = $met->bicodigo;
                    $colHora[$idCol] = $met->bidataevento;
                    $colStatus[$idCol] = $met->biignicao;

            }
        }

        if(!empty($ligado)) {
            foreach($ligado as $f => $lig) {
                $bidate = new \DateTime($lig->bidataevento);
                if($bidate == new \DateTime($hfinal[$f]->bidataevento)) continue;
                $cStatus = explode(',', $colStatus[$f]);
                $cHora = explode((','), $colHora[$f]);
                foreach($cStatus as $l => $cStatu) {
                    $dcHora = new \DateTime($cHora[$l]);
                    if($cStatu == 1 && new \DateTime($dcHora->diff($bidate)->format('%H:%I:%S')) > new \DateTime('00:30:00')) {
                        $horaIntervalo[$f] = $dcHora->diff($bidate)->format('%H:%I:%S');
    				    $saiuIntervalo[$f] = $bidate->format('d/m/Y H:i:s');
                        $voltaIntervalo[$f] = $dcHora->format('d/m/Y H:i:s');
                        break;
                    }
                }
                if(empty($horaIntervalo[$f])) {
                    $id = $lig->bimotorista;
                    $time = array_pop($cHora);
                    $data = new \DateTime($time);
                    $data->add(new \DateInterval('P1D'));
                    $data = $data->format('d/m/Y');
                    $parada = Bilhete::select('bicodigo', 'bidataevento', 'bimotorista', 'biignicao')
                        ->where('bidataevento', '>', $time)
                        ->where('bidataevento', '<', $data)
                        ->where('biignicao', '=', 1)
                        ->where('bimotorista', '=', $id)
                        ->orderBy('bidataevento', 'asc')
                        ->first();
                    if($parada){
                        $part = new \DateTime($parada->bidataevento);
                    }else{
                        $part = $dia;
                    }
                    $horaIntervalo[$f] = $part->diff($bidate)->format('%H:%I:%S');
                    $saiuIntervalo[$f] = $bidate->format('d/m/Y H:i:s');
                    $voltaIntervalo[$f] = $part->format('d/m/Y H:i:s');
                }
            }
        }

        $dsr = 0;

        foreach($horario as $q => $hrs) {
            $extraNoturnoManha = '00:00:00';
            $extraNoturnoTarde = '00:00:00';
            $extraManha = '00:00:00';
            $extraTarde = '00:00:00';
            $extraTardeCinquenta = '00:00:00';
            $extraManhaCinquenta = '00:00:00';
            $faltaManha = '00:00:00';
            $faltaTarde = '00:00:00';
            $inicio = $hrs->bidataevento;
            if($inicio == null) continue;
            $id = $hrs->bimotorista;
            $inicioTotal = new \DateTime($inicio);
            $fim = isset($hfinal[$q]) ? $hfinal[$q]->bidataevento : '';
            if(empty($fim))
                $fim = $dia->format('Y-m-d').' 00:00:00';
            $fimTotal = new \DateTime($fim);
            // dd($hrs);
            // $inicioExpediente = new \DateTime($inicioTotal->format('Y-m-d').' 08:00:00');
            // $fimExpediente = new \DateTime($inicioTotal->format('Y-m-d'). ' 18:00:00');
            $inicioExpediente = new \DateTime($hrs->hjtiniprimeirot);
            $intervaloInicio = new \Datetime($hrs->hjtfimprimeirot);
            $intervaloFim = new \Datetime($hrs->hjtinisegundot);
            $fimExpediente = new \DateTime($hrs->hjtfimsegundot);
            // dd($intervaloInicio, $intervaloFim, $tempoIntervalo);
            $adNoturnoManha = new \DateTime($inicioTotal->format('Y-m-d'). ' 05:00:00');
            $adNoturnoTarde = new \DateTime($fimTotal->format('Y-m-d'). ' 22:00:00');
            $jornada = new \DateTime($inicioTotal->diff($fimTotal)->format('%H:%I:%S'));
            $tempoIntervalo = $intervaloInicio->diff($intervaloFim)->format('%H:%I:%S');
            $totalIntervalo = new \DateTime($tempoIntervalo);
            if(empty($hrs->hjtinisegundot)) {
       //          $inicioExpediente = new DateTime($inicioTotal->format('Y-m-d'). ' 08:00:00');
    			$fimExpediente = new \DateTime($hrs->hjtfimprimeirot);
	    		$totalIntervalo = new \DateTime('00:00:00');
            }
            if(date('w', strtotime($inicio)) == 0) {
                $dsr = 1;
                $inicioExpediente = new \DateTime($inicioTotal->format('Y-m-d'). ' 08:00:00');
                $fimExpediente = new \DateTime($inicioTotal->format('Y-m-d'). '08:00:00');
                $totalIntervalo = new \DateTime('00:00:00');
            }
            $horasTotal = $fimExpediente->diff($inicioExpediente)->format('%H:%I:%S');
            $totalHoras = gmdate('H:i:s', strtotime($horasTotal) - strtotime($totalIntervalo->format('H:i:s')));
            if($inicioExpediente > $inicioTotal && $jornada > new \DateTime('00:00:00')) {
                $extraManha = $inicioExpediente->diff($inicioTotal)->format('%H:%I:%S');
                $extraManhaTime = new \DateTime($extraManha);
                $extraManhaCinquenta =  new \DateTime($inicioExpediente->diff($adNoturnoManha)->format('%H:%I:%S'));

                if($extraManhaTime > $extraManhaCinquenta) {
                    $extraNoturnoManha = $extraManhaTime->diff($extraManhaCinquenta)->format('%H:%I:%S');
                    $extraManha = $inicioExpediente->diff($adNoturnoManha)->format('%H:%I:%S');
                }
            }
            if($fimExpediente < $fimTotal && $jornada > new \DateTime($horasTotal) && $inicioTotal < $fimExpediente) {
                $extraTarde = $fimExpediente->diff($fimTotal)->format('%H:%I:%S');
                $extraTardeTime = new \DateTime($extraTarde);
                $extraTardeCinquenta = new \DateTime($fimExpediente->diff($adNoturnoTarde)->format('%H:%I:%S'));
                if($extraTardeTime > $extraTardeCinquenta) {
                    $extraNoturnoTarde = $extraTardeTime->diff($extraTardeCinquenta)->format('%H:%I:%S');
                    $extraTarde = $fimExpediente->diff($adNoturnoTarde)->format('%H:%I:%S');
                }
            }
            if($inicioExpediente < $inicioTotal) {
                $faltaManha = $inicioExpediente->diff($inicioTotal)->format('%H:%I:%S');
            }
            if($fimExpediente > $fimTotal) {
                $faltaTarde = $fimExpediente->diff($fimTotal)->format('%H:%I:%S');
            }
            $extra = $dataHelper->somaHora([$extraTarde, $extraManha]);
            $extraNoturno = $dataHelper->somaHora([$extraNoturnoTarde, $extraNoturnoManha]);
            $horaFalta = $dataHelper->somaHora([$faltaTarde, $faltaManha]);
            if(new \DateTime($horaFalta) > new \DateTime($totalHoras))
                $horaFalta = $totalHoras;
            $hParado = isset($horasParado[$q]) ? $horasParado[$q] : '00:00:00';

            if($hrs->bidataevento != $ligouTarde[$q]->bidataevento) {
                $hIntervalo = isset($horaIntervalo[$id]) ? $horaIntervalo[$id] : '00:00:00';
                $timeHIntervalo = new \DateTime($hIntervalo);

                if($timeHIntervalo < $totalIntervalo) {
                    $extra = $dataHelper->somaHora([$timeHIntervalo->diff($totalIntervalo)->format('%H:%I:%S'), $extra]);
                }else if($timeHIntervalo > $totalIntervalo) {
                    $horaFalta = $dataHelper->somaHora([$timeHIntervalo->diff($totalIntervalo)->format('%H:%I:%S'), $horaFalta]);
                }
                $timeParado = new \DateTime($hParado);
                if($timeParado > new \DateTime('00:00:00'))
                    $hParado = $timeParado->diff($timeHIntervalo)->format('%H:%I:%S');
                $horasInt = new \DateTime($hIntervalo);
                $trabalhadas = $jornada->diff($horasInt)->format('%H:%I:%S');
                $sIntervalo = isset($saiuIntervalo[$id]) ? $saiuIntervalo[$id] : '00:00:00';
                $vIntervalo = isset($voltaIntervalo[$id]) ? $voltaIntervalo[$id] : '00:00:00';

                $fechamento = new FechamentoFolha;
                $fechamento->fehoratrabalhada = $trabalhadas ? : '00:00:00';
                $fechamento->fedataentrada = $inicio;
                $fechamento->fefimexpediente = $fim;
                $fechamento->femotorista = $id;
                $fechamento->fedsr = $dsr;
                $fechamento->fehoraextra = $extra ? : '00:00:00';
                $fechamento->feextranoturno = $extraNoturno ? : '00:00:00';
                $fechamento->fehorafalta = $horaFalta ? : '00:00:00';
                $fechamento->fehoraespera = $hParado ? : '00:00:00';
                $fechamento->feintervalo = $hIntervalo ? : '00:00:00';
                $fechamento->fehoracemcento;
                $fechamento->save();
                echo 'Salvo com sucesso!';
            }
        }
    }
    public function cliente(Request $request)
    {
        $id = $request->id;
        if(empty($id)) {
            return response ([
                'motoristas' => '',
            ]);
        }
        $motoristas = Cliente::select('mtcodigo', 'mtnome')
            ->join('motoristas', 'mtcliente', '=', 'clcodigo')
            ->whereIn('clcodigo', $id)->get();

        return response ([
            'motoristas' => $motoristas,
        ]);
    }
}

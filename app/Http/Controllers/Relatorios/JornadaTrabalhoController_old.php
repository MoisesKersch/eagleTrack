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
        $jornada = $this->query($request);
        foreach ($jornada as $i => $jorm) {
            $total = new \Datetime($jorm['dfehoratrabalhada']);
            $espera = new \Datetime($jorm['jfehoraespera']);
            $trab = gmdate('H:i:s', strtotime($total->format('H:i:s')) - strtotime($espera->format('H:i:s')));
            $trabalhadas = gmdate('H:i:s', strtotime($trab) - strtotime($jorm['kfeintervalo']));
            $jornada[$i] += ['trabalhadas' => $trabalhadas];
        }
        return response([
            'jornada' => $jornada,
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
        $nunDia = date('w', strtotime($dia->format('Y-m-d')));
        $final= $dia->format('Y/m/d').' 00:00:00';
        $inicio = $data.' 00:00:00';
        $ids = DB::select("select distinct on (bimotorista) bimotorista from bilhetes
                join veiculos on veplaca = biplaca
                where bimotorista > 0
                and bidataevento > '$inicio'
                and bidataevento < '$final'
                and veproprietario in (20,22,26,27,29,34)");

        $comeco = Bilhete::select('bimotorista', DB::raw('min(bidataevento) as bidataevento'), 'biplaca', 'hj.*')
            ->join('motoristas', 'mtcodigo', '=', 'bimotorista')
            ->join('horas_jornada_trabalho as hj', 'mtjornada', 'hjtjornada')
            ->where('bimotorista', '>', 0)
            ->where('hjtdiasemana', '=', $nunDia)
            ->where('bidataevento', '>', $inicio)
            ->where('bidataevento', '<', $final)
            ->where('biignicao', '=', 1)
            ->whereIn('mtcliente', [20,22,26,27,29,34])
            ->groupBy('bimotorista', 'biplaca', 'mtnome', 'hj.hjtcodigo')
            ->orderBy('bimotorista')
            ->get();
        foreach ($comeco as $i => $ini) {
            $horario[$ini->bimotorista] = $ini;
        }

        if(!isset($horario)) return "nada consta!";

        $ligaTarde = DB::select("select bimotorista,
            max(bidataevento) bidataevento
            from bilhetes
            join veiculos on veplaca = biplaca
            where bimotorista > 0
            and bidataevento > '$inicio'
            and bidataevento < '$final'
            and veproprietario in (20,22,26,27,29,34)
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
            $hfim = $finalHora->bidataevento;
            $hespera = \DB::table('bilhetes as bi')
                ->select('bidataevento as comeco')
                ->addSelect(\DB::raw("(select bidataevento from bilhetes as bl
                                    where bl.bidataevento > bi.bidataevento
                                    and bimotivotransmissao = 9
                                    and bimotorista = '$id'
                                    order by bidataevento asc
                                    limit 1) as fim"))
                ->where('bimotorista', $id)
                ->where('bidataevento', '>', $hcomeco)
                ->where('bidataevento', '<', $hfim)
                ->where('bimotivotransmissao', 10)
                ->orderBy('bidataevento', 'ASC')
                ->get();
            $parado = [];
            foreach ($hespera as $i => $esp) {
                $comeco = new \Datetime($esp->comeco);
                $fim = new \Datetime($esp->fim);
                $parado[] = $comeco->diff($fim)->format('%H:%I:%S');
            }

            $horasParado[$liga->bimotorista] = $dataHelper->somaHora($parado);
        }
        $chave ='';
        foreach ($horario as $k => $hor) {
            if(!empty($hor->hjtinisegundot)){
                $iintervalo[$k] = Bilhete::select('bidataevento', 'bimotorista')
                    ->where('bidataevento', '>', $data.' '.$hor->hjtfimprimeirot)
                    ->where('bidataevento', '<', $data.' '.$hor->hjtinisegundot)
                    ->where('bimotorista', '=', $hor->bimotorista)
                    ->where('biignicao', '=', 0)
                    ->orderBy('bidataevento', 'asc')
                    ->first();
                if(empty($iintervalo[$k])) continue;
                $fintervalo[$k] = Bilhete::select('bidataevento')
                    ->where('bidataevento', '>', $iintervalo[$k]->bidataevento)
                    ->where('bidataevento', '<', $data.' '.$hor->hjtinisegundot)
                    ->where('bimotorista', '=', $iintervalo[$k]->bimotorista)
                    ->where('bimotivotransmissao', '=', 9)
                    ->first();
                if(empty($fintervalo[$k])){
                    $fintervalo[$k] = new \Datetime($data.' '.$hor->hjtinisegundot);
                }else{
                    $fintervalo[$k] = new \Datetime($fintervalo[$k]->bidataevento);
                }
                $iintervalo[$k] = new \Datetime($iintervalo[$k]->bidataevento);
                $horaIntervalo[$k] = $iintervalo[$k]->diff($fintervalo[$k])->format('%H:%I:%S');

            $chave .= $k.',';
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
            $inicioExpediente = new \DateTime($data.' '.$hrs->hjtiniprimeirot);

            $intervaloInicio = new \Datetime($data.' '.$hrs->hjtfimprimeirot);
            $intervaloFim = new \Datetime($data.' '.$hrs->hjtinisegundot);

            $fimExpediente = new \DateTime($data.' '.$hrs->hjtfimsegundot);
            $adNoturnoManha = new \DateTime($inicioTotal->format('Y-m-d'). ' 05:00:00');
            $adNoturnoTarde = new \DateTime($fimTotal->format('Y-m-d'). ' 22:00:00');

            $jornada = new \DateTime($inicioTotal->diff($fimTotal)->format('%H:%I:%S'));
            $tempoIntervalo = $intervaloInicio->diff($intervaloFim)->format('%H:%I:%S');
            $totalIntervalo = new \DateTime($tempoIntervalo);
            if(empty($data.' '.$hrs->hjtinisegundot)) {
                $fimExpediente = new \DateTime($data.' '.$hrs->hjtfimprimeirot);
                $totalIntervalo = new \DateTime('00:00:00');
            }
            if(date('w', strtotime($inicio)) == 0) {
                $dsr = 1;
                $inicioExpediente = new \DateTime($data.' '.$hrs->hjtiniprimeirot);
                $fimExpediente = new \DateTime($data.' '.$hrs->hjtfimsegundot);
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

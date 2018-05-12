<?php

namespace App\Services;

use DB;
use DateTime;
use App\Helpers\DataHelper;
use App\Models\FechamentoFolha;
use App\Models\Bilhete;
use App\Models\Cliente;
use App\Models\Motorista;
use App\Models\IgnicaoVeiculos;
use App\Models\Pontos;

class JornadaTrabalhoLivreService
{
	public function script($request)
	{
		$dataHelper = new DataHelper;

        // $consultaMotoristas = Motorista::select(DB::raw(
        //         'mtcodigo,

        //             array(select hjt.hjttotalhoras from jornada_trabalho jt left join horas_jornada_trabalho hjt on (hjt.hjtjornada = jt.jtcodigo) where jtcliente = mtcliente and jt.jttipo = \'L\') as hjttotalhoras,

        //             array(select hjt.hjtintervalo from jornada_trabalho jt left join horas_jornada_trabalho hjt on (hjt.hjtjornada = jt.jtcodigo) where jtcliente = mtcliente and jttipo = \'L\') as hjtintervalo,

        //             array(select hjt.hjtdiasemana from jornada_trabalho jt left join horas_jornada_trabalho hjt on (hjt.hjtjornada = jt.jtcodigo) where jtcliente = mtcliente and jttipo = \'L\') as hjtdiasemana,

        //             array(select phponto from pontos_hora_espera_cliente where phcliente = mtcliente) as phponto'
        //     ))
        //     ->where('mtstatus', '=', 'A')
        //     ->get();


        $dia = $request->d;
        $dia = '01-11-2017';
        $dataHelper = new DataHelper;
        $dia = new Datetime($dia ? : '');
        $data = date('Y-m-d', strtotime('-1 days', strtotime($dia->format('Y/m/d'))));
        $nunDia = date('w', strtotime($data));
        $final= $dia->format('Y/m/d').' 00:00:00';
        $inicio = $data.' 00:00:00';

        // $ajudantes = IgnicaoVeiculos::select('hj.*', 'mtcodigo', 'mtperfil', 'ivcliente', 'ivdataevento', 'ivmotorista', 'ivajudante', 'ivmotivotransmissao')
        //     ->join('motoristas', 'mtcodigo','=','ivajudante')
        //     ->join('clientes', 'clcodigo', '=', 'ivcliente')
        //     ->rightJoin('horas_jornada_trabalho as hj', 'mtjornada', 'hjtjornada')
        //     ->with('cliente.pontosEspera')
        //     ->where('ivdataevento', '>', $inicio)
        //     ->where('ivdataevento', '<', $final)
        //     ->where('hjtdiasemana', $nunDia)
        //     ->whereRaw('ivponto not in (select phponto from pontos_hora_espera_cliente)')
        //     ->orderBy('ivmotorista', 'ASC')
        //     ->orderBy('ivdataevento','ASC')
        //     ->get();

        // dd($ajudantes);


        //esse \/

        $ajudantes = IgnicaoVeiculos::select('hj.*', 'mtcodigo', 'mtperfil', 'ivcliente', 'ivdataevento', 'ivmotorista', 'ivmotivotransmissao')
            ->join('motoristas', 'mtcodigo','=','ivmotorista')
            ->join('clientes', 'clcodigo', '=', 'ivcliente')
            ->rightJoin('horas_jornada_trabalho as hj', 'mtjornada', 'hjtjornada')
            ->with('cliente.pontosEspera')
            ->where('ivdataevento', '>', $inicio)
            ->where('ivdataevento', '<', $final)
            ->where('hjtdiasemana', $nunDia);
            // ->whereNotNull('hj.hjttotalhoras');

        $ajudantes->where(function($query) {
            $query->whereRaw('ivponto not in (select phponto from pontos_hora_espera_cliente) or ivponto is null');
        });

        $ajudantes->orderBy('ivmotorista', 'ASC')
            ->orderBy('ivdataevento','ASC');

        $ajudantes = $ajudantes->get();
        // $ajudantes = $ajudantes->toSql();

        dd($ajudantes[0], $nunDia);

        // $consultaMotoristas = Motorista::select(DB::raw(
        //         'mtcodigo, hjttotalhoras, hjtintervalo, hjtdiasemana, phponto'
        //     ))
        //     ->rightJoin('jornada_trabalho', 'jtcodigo', '=', 'mtjornada')
        //     ->rightJoin('horas_jornada_trabalho', 'hjtjornada', '=', 'jtcodigo')
        //     ->leftJoin('pontos_hora_espera_cliente', 'phcliente', '=', 'mtcliente')
        //     ->where('jttipo', '=', 'L')
        //     ->where('jtstatus', '=', 'A')
        //     ->where('mtstatus', '=', 'A')
        //     ->orderBy('mtcodigo', 'ASC')
        //     ->get()->toArray();

        // if (count($consultaMotoristas) < 1)
        //     return 'Nenhum motorista encontrado!';

        // dd($consultaMotoristas);

        // $bilhetes = Bilhete::select('bidataevento', 'bimotorista', 'bimotivotransmissao')
        //     ->join('veiculos', 'veplaca', '=', 'biplaca')
        //     ->whereBetween('bidataevento', [$inicio, $final])
        //     ->whereIn('bimotivotransmissao', [9,10])
        //     ->where('bimotorista', '>', 0)
            // ->orderBy('bimotorista', 'ASC')
            // ->orderBy('bidataevento','ASC')
        //     ->limit(1000)
        //     ->get();

        // if (count($bilhetes) < 1)
        //     return 'Nenhuma ignição encontrada!';

        // $motivotrans = $bilhetes[0]->bimotivotransmissao;
        // $motorista = $bilhetes[0]->bimotorista;
        // foreach ($bilhetes as $k => $bilhete) {
        //     if ($motorista != $bilhete->bimotorista) {

        //         continue;
        //     }

        //     if ($motivotrans != $bilhete->bimotivotransmissao) {
        //         $horariosMotoristas[$motorista['mtcodigo']][] = [];
        //         continue;
        //     }
        // }

        // TODOS OS BILHETES VAO SER DA MESMA DATA
        $ignicaoVeiculo =
            IgnicaoVeiculos::whereBetween('ivdataevento', [$inicio, $final])
                ->where('ivmotorista', '<>', 0)
                ->orderBy('ivmotorista', 'ASC')
                ->orderBy('ivplaca')
                ->orderBy('ivdataevento','ASC')
                ->limit(1000)
                ->get()->toArray();

        if (count($ignicaoVeiculo) < 1)
            return 'Nenhuma ignição encontrada!';

        $rpontos = Pontos::get()->toArray();
        $pontos = [];
        foreach ($rpontos as $key => $ponto) {
            $pontos[$ponto['pocodigo']] = $ponto;
        }

        $motoristas = [];
        foreach ($consultaMotoristas as $key => $motorista) {
            // $motoristas[$motorista['mtcodigo']][$motorista['hjtdiasemana']] = [
            //     'diaria' => $motorista['hjttotalhoras'],
            //     'intervalo' => $motorista['hjtintervalo'],
            //     'ponto' => $motorista['phponto']
            // ];
            $teste = json_decode($motorista['hjtdiasemana']);
            var_dump($teste);exit;
            if (is_array(json_decode($motorista['hjtdiasemana']))) {
                foreach (json_decode($motorista['hjtdiasemana']) as $k => $value) {
                    dd($value);
                }
            }

            // $motoristas[$motorista['mtcodigo']][] = [
            //     'diaria' => $motorista['hjttotalhoras'],
            //     'intervalo' => $motorista['hjtintervalo'],
            //     'ponto' => $motorista['phponto']
            // ];
        }exit;

        $horariosMotoristas = [];
        $motivotrans = $ignicaoVeiculo[0]['ivmotivotransmissao'];
        $motorista = $ignicaoVeiculo[0]['ivmotorista'];
        $placa = $ignicaoVeiculo[0]['ivplaca'];

        $horaDiaria = 0;

        $intervalo = false;
        $intervaloHora = 0;
        $intervaloH = 0;
        $intervaloM = 0;
        $intervaloS = 0;

        $horaTmp = 0;
        $minTmp = 0;
        $segTmp = 0;
        $tempoTrabalhadoTmp = 0;

        $horaFalta = 0;
        $horaExtra = 0;
        $extra100 = 0;
        $adicionalNoturno = 0;
        $extraNoturno = 0;
        $horaEspera = 0;
        //Adiciona horario
        if ($motivotrans == 10) {
            $explode = explode(' ', $ignicaoVeiculo[0]['ivdataevento']);
            $ignicaoTmp = [
                'ivdataevento' => $explode[0].' 00:00:00',
                'ivmotorista' => $ignicaoVeiculo[0]['ivmotorista'],
                'ivmotivotransmissao' => 9,
                'ivcliente' => $ignicaoVeiculo[0]['ivcliente'],
                'ivponto' => 0,
                'ivajudante' => $ignicaoVeiculo[0]['ivajudante'],
                'ivplaca' => $ignicaoVeiculo[0]['ivplaca']
            ];
            array_unshift($ignicaoVeiculo, $ignicaoTmp);
            $motivotrans = 9;
        }

        $horaInicio = new DateTime($ignicaoVeiculo[0]['ivdataevento']);

        foreach ($ignicaoVeiculo as $k => $bilhete) {
            //Quando altera a ignição entra no if, ignorando ignições duplicadas
            if ($motorista == $bilhete['ivmotorista']) {
                $dateFinal = new DateTime($bilhete['ivdataevento']);

                if ($motivotrans != $bilhete['ivmotivotransmissao']) {
                    $motivotrans = $bilhete['ivmotivotransmissao'];
                    //Desligou
                    if ($bilhete['ivmotivotransmissao'] == 9) {
                        $horaInicio = new DateTime($bilhete['ivdataevento']);
                        //Se tiver com hora do intervalo inicializado, vai somar e add na variável
                        if ($intervalo) {
                            $intervaloH += (int)$intervaloHora->diff($dateFinal)->format("%H");
                            $intervaloM += (int)$intervaloHora->diff($dateFinal)->format("%I");
                            $intervaloS += (int)$intervaloHora->diff($dateFinal)->format("%S");

                            $horaDiaria = date("H:i:s", mktime(($horaTmp),($minTmp),($segTmp),0,0,0));
                            $horariosMotoristas[$bilhete['ivmotorista']][$horaInicio->format('d/m/Y')] = [
                                'intervalo' => $horaDiaria
                            ];
                        }
                        continue;
                    }

                    var_dump($motoristas[$bilhete['ivmotorista']]);

                    //Bilhete em um ponto?
                    if (isset($motoristas[$bilhete['ivmotorista']]['phponto'])) {
                        if ($motoristas[$bilhete['ivmotorista']]['phponto']['potipo'] == 'P') {
                            //Dentro do if é intervalo
                            $intervalo = true;
                            $intervaloHora = new DateTime($bilhete['ivdataevento']);
                        } else {
                            //Hora espera ou trabalhada (Verificar parametros)
                            dd('aqui');
                        }
                    } else {
                        //Tempo não trabalhado (Parou de trabalhar)
                        $dateFinal = new DateTime($bilhete['ivdataevento']);
                        $horaTmp += (int)$horaInicio->diff($dateFinal)->format("%H");
                        $minTmp += (int)$horaInicio->diff($dateFinal)->format("%I");
                        $segTmp += (int)$horaInicio->diff($dateFinal)->format("%S");

                        $horaDiaria = date("H:i:s", mktime(($horaTmp),($minTmp),($segTmp),0,0,0));
                        $horariosMotoristas[$bilhete['ivmotorista']][$dateFinal->format('d/m/Y')] = [
                            'trabalhada' => $horaDiaria
                        ];
                    }
                }

                continue;
            }

            $intervalo = false;
            $motivotrans = $bilhete['ivmotivotransmissao'];
            // var_dump();

            //Troca motorista, zera as variaveis
            if ($motorista != $bilhete['ivmotorista']) {
                $horaDiaria = 0;
                $intervalo = 0;
                $horaFalta = 0;
                $horaExtra = 0;
                $extra100 = 0;
                $adicionalNoturno = 0;
                $extraNoturno = 0;
                $horaEspera = 0;

                $intervalo = false;
                $intervaloHora = 0;
                $intervaloH = 0;
                $intervaloM = 0;
                $intervaloS = 0;

                $horaTmp = 0;
                $minTmp = 0;
                $segTmp = 0;
                $tempoTrabalhadoTmp = 0;

                $motorista = $bilhete['ivmotorista'];

                if ($bilhete['ivmotivotransmissao'] == 10) {

                }

                continue;
            }

            $motivotrans = $bilhete['ivmotivotransmissao'];
            $motorista = $bilhete['ivmotorista'];
        }

        dd($horariosMotoristas, 'lalal');

        // $fechamento = new fechamentofolha;
        // $fechamento->fehoratrabalhada =$horasMot[$j];
        // $fechamento->fehorastotal = $trabalhadas;
        // $fechamento->fedataentrada = $mtInicio[$j];
        // $fechamento->fefimexpediente = $mtFim[$j];
        // $fechamento->femotorista = $j;
        // $fechamento->fedsr = $nunDia == 0 ? 1 : 0;
        // $fechamento->fehoraextra = $extra;
        // $fechamento->feextranoturno = $extraNoturnoReal;
        // $fechamento->fehoranoturna = $adicionalRealTrab;
        // $fechamento->fehorafalta = $falta;
        // $fechamento->fehoraespera = $espera;
        // $fechamento->feintervalo = $horasInter[$j];
        // $fechamento->save();

	}
}

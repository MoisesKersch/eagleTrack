<?php

namespace App\Services;

use DB;
use App\Helpers\DataHelper;
use App\Helpers\MapaHelper;
use DateTime;

class AcionamentoPortasService
{
    public function getAcionamentoPortas($request)
    {
        $placa = $request->placa;
        $dataInicial = $request->dataIni;
        $dataFinal = $request->dataFim;

        $acps = \DB::table('bilhetes as b')
            ->select(DB::raw("b.bilatlog as posicao, to_char(min(b.bidataevento), 'DD/MM HH24:MI')
            as data,coalesce(b.biendereco, 'Sem endereço') as endereco, bimotivotransmissao"))
            ->leftJoin('veiculos as v', 'biplaca', '=', 'veplaca')
            ->where('biplaca','=', $placa)
            ->where('bidataevento', '>', $dataInicial)
            ->where('bidataevento', '<', $dataFinal)
            //->where('bimovimento', '=', 0)
            //aberta - desconsiderar [1 - 14, 2 - 16, 3 - 18, 4 - 20]
            //ativada (fechada ou nao) [1 - 13, 2 - 15, 3 - 17, 4 - 19]
            ->whereIn('bimotivotransmissao', [14,16,18,20])
            ->groupBy(DB::raw('bidataevento,posicao, endereco, bimotivotransmissao'))
            ->orderBy(DB::raw('data'))
            ->get();

        return response ([
            'array' => $acps
        ]);
    }

    public function query($request)
    {
        $clientes = $request->clientes;
        $dataInicial = $request->data_inicio;
        $dataFinal = $request->data_fim;
        $paramPortas = explode(',', $request->portas);
        $veiculos = $request->buscar;

        $achouAtivo = false;
        $countacp = 0;
        $somadatadia = 0;
        $somadataplaca = 0;
        $portas = [];
        $datahelper = new DataHelper;
        $mapahelper = new MapaHelper;
        $arrayPortas = [
            13 => '1',
            15 => '2',
            17 => '3',
            19 => '4'
        ];
        $kpaa = null;
        $arrayDatas = 0;
        $arrayDatasPlacas = 0;
        $keyPorta = 0;
        $arrayDatasPlacasTmp = 0;

        $acps = \DB::table('bilhetes as b')
            ->select(DB::raw("b.bilatlog as posicao, to_char(min(b.bidataevento), 'DD/MM/YYYY HH24:MI') as data,
                coalesce(b.biendereco, 'Sem endereço') as endereco, bimotivotransmissao as bimt, concat_ws(' | ',veplaca, veprefixo) as veplaca , veproprietario, veprefixo"))
            ->leftJoin('veiculos as v', 'biplaca', '=', 'veplaca')
            ->whereIn('vecodigo', $veiculos)
            ->whereIn('veproprietario', $clientes)
            ->where('bidataevento', '>=', $dataInicial.' 00:00')
            ->where('bidataevento', '<=', $dataFinal.' 23:59')
            //aberta - desconsiderar [1 - 14, 2 - 16, 3 - 18, 4 - 20]
            //ativada (fechada ou nao) [1 - 13, 2 - 15, 3 - 17, 4 - 19] 14,16,18,20,13,15,17,19
            ->whereIn('bimotivotransmissao', [(int)$paramPortas[0], (int)$paramPortas[1]])
            ->groupBy(DB::raw('bidataevento,posicao, endereco, bimotivotransmissao, veplaca, veproprietario, veprefixo'))
            ->orderBy(DB::raw('veplaca, bidataevento'))
            ->get();

        $pontosTmp = \DB::table('pontos as p')
            ->select(DB::raw('pocodigo, podescricao, polatitude, polongitude, poraio, pocodigocliente'))
            ->whereIn('pocodigocliente', $clientes)
            ->get();

        $pontos = [];
        foreach($pontosTmp as $tmp) {
            $pontos[$tmp->pocodigocliente][] = array(
                'pocodigo' => $tmp->pocodigo,
                'podescricao' => $tmp->podescricao,
                'polatitude' => $tmp->polatitude,
                'polongitude' => $tmp->polongitude,
                'poraio' => $tmp->poraio
            );
        }

        $countAcps = count($acps);
        foreach ($acps as $key => $acp) {
            if ($acp->bimt%2 == 0 && $achouAtivo == false) {
                $achouAtivo = true;
                $dataPortaAberta = $acp->data;
                $acps[$key]->horaInicio = (explode(' ', $acp->data))[1];
                $keyPorta = $key;
                $keyPortaAnt = $key;
                $arrayUltimaAbertura = [];
                for ($i = $key; $i < $countAcps; $i++) {
                    if ($acps[$i]->bimt == 13) {
                        $arrayUltimaAbertura[] = $i;
                    }
                }
                continue;
            }

            if ($acp->bimt%2 != 0 && $achouAtivo == true) {
                $achouAtivo = false;
                if ($dataPortaAberta > $acp->data && $acps[$keyPorta]->veplaca != $acp->veplaca) {
                    $diffDatas = 0;
                    $tmpDiffDatas = '';
                    $acps[$keyPorta]->horaFinal = '';
                } else {
                    $diffDatas = $datahelper->diferencaDatas($dataPortaAberta, $acp->data);
                    $tmpDiffDatas = $datahelper->converteSegundosPorExtenso(($diffDatas));
                    $acps[$keyPorta]->horaFinal = (explode(' ', $acp->data))[1];
                }

                $arrayDatas = $arrayDatas+$diffDatas;
                $acps[$keyPorta]->tmpPortaAberta = $tmpDiffDatas;
                $acps[$keyPorta]->dataFinal = $acp->data;
                $dataPortaAbertaAbrev = (explode(' ', $acps[$keyPorta]->data))[0];
                $proxDataPortaAbertaAbrev = $key < $countAcps-1 ? (explode(' ', $acps[$key+1]->data))[0] : $dataPortaAbertaAbrev;

                $acps[$keyPorta]->numPorta = $arrayPortas[$paramPortas[0]];
                $tmpPosicao = explode(',', $acps[$keyPorta]->posicao);
                $tmpPonto = isset($pontos[$acps[$keyPorta]->veproprietario]) ? $mapahelper->buscarPontoProximo($tmpPosicao, $pontos[$acp->veproprietario]) : false;
                $acps[$keyPorta]->localposicao = $tmpPonto ? $tmpPonto['nomePonto'] : '';

                $portas[$acps[$keyPorta]->veplaca][$dataPortaAbertaAbrev][] = $acps[$keyPorta];
                $countacp++;

                if ($dataPortaAbertaAbrev != $proxDataPortaAbertaAbrev || count($arrayUltimaAbertura) == 1 || $key+1 >= $countAcps) {
                    $tmpDiffDatas = $datahelper->converteSegundosPorExtenso(($arrayDatas));
                    $arrayDatasPlacas += $arrayDatas;
                    $portas[$acps[$keyPorta]->veplaca][$dataPortaAbertaAbrev]['countPortasAc'] = $countacp;
                    $portas[$acps[$keyPorta]->veplaca][$dataPortaAbertaAbrev]['tempoAbertoData'] = $tmpDiffDatas;
                    $countacp = 0;
                    $arrayDatas = 0;
                }
            }

            if ($key+2 >= $countAcps || $acps[$keyPorta]->veplaca != $acp->veplaca) {
                if (!isset($portas[$acps[$keyPorta]->veplaca]['tempoAbertoPlaca'])) {
                    $arrayDatasPlacasTmp = 0;
                }

                $arrayDatasPlacas += $arrayDatasPlacasTmp;
                $tmpDiffDatasPlaca = $datahelper->converteSegundosPorExtenso(($arrayDatasPlacas));
                $portas[$acps[$keyPorta]->veplaca]['tempoAbertoPlaca'] = $tmpDiffDatasPlaca;
                $arrayDatasPlacasTmp = $arrayDatasPlacas;
                $arrayDatasPlacas = 0;
            }
        }
        // dd($portas);
        return response ([
            'array' => $portas
        ]);
    }
}

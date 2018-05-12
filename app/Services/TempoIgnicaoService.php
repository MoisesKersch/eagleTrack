<?php

namespace App\Services;

use DB;
use App\Models\Bilhete;
use Auth;
use App\Helpers\DataHelper;

class TempoIgnicaoService
{
	public function query($request)
    {
    	$inicio = $request->data_inicio.' 00:00:00';
    	$fim = $request->data_fim.' 23:59:59';
    	$bilhetes = Bilhete::select(DB::raw("to_char(bidataevento,'DD/MM/YYYY') as data,
    										 to_char(bidataevento,'HH24:MI:SS') as hora,
    										 to_char(bidataevento,'DD/MM/YYYY HH24:MI:SS') as bidataevento,
    										 biignicao,
    										 biplaca,
											 veprefixo,
    										 coalesce(mtnome,'Não Associado') as motorista"
    										)
    								)
    			 ->leftJoin('motoristas','bimotorista','=','mtcodigo')
    			 ->leftJoin('veiculos','biplaca','=','veplaca')
    			 ->whereBetween('bidataevento',[$inicio,$fim])
    			 ->whereIn('bimotivotransmissao',[9,10])
				 ->whereIn('vecodigo',$request->buscar)
				 ->orderBy('bidataevento');
		$bilhetes = $bilhetes->get();
		//agrupamento dados por placa, data
		$arrBilhetesAgp = array();
		foreach($bilhetes as $key=>$bilhete){
			$arrBilhetesAgp[$bilhete->biplaca][$bilhete->data][] = $bilhete;
		}
		//tratamento evento
		$dataHelper = new DataHelper;
		$arrRetorno = array();//recebe somente dados tratados
		foreach($arrBilhetesAgp as $placa=>$placas){
			foreach($placas as $data=>$datas){
				$placa = $placa." | ".$datas[0]->veprefixo;
				$achouLigado = false;
				$dataHoraAnterior = "";
				$horaAnterior = "";
				$tempoIgnicaoLigada = " ";
				$totalTempoDia = 0;
				foreach($datas as $key=>$linha){
					if($linha->biignicao == 1 && $achouLigado == false){
						$achouLigado = true;
						$dataHoraAnterior = $linha->bidataevento;
						$horaAnterior = $linha->hora;
					}
					if($achouLigado == true && $linha->biignicao == 0){
						$tempoIgnicaoLigada = $dataHelper->diferencaDatas($dataHoraAnterior,$linha->bidataevento);
						$achouLigado = false;
						$totalTempoDia += (int)$tempoIgnicaoLigada;
						$arrRetorno[$placa][$data][] = array('tempo'      => $dataHelper->converteSegundosPorExtenso($tempoIgnicaoLigada),
															 'dataInicio' => $dataHoraAnterior,
															 'dataFim'    => $linha->bidataevento,
															 'horaI'      => $horaAnterior,
															 'horaF'      => $linha->hora,
															 'motorista'  => $linha->motorista,
															 'placa'	  => $linha->biplaca,
															);
					}
				}
				$arrRetorno[$placa][$data]['tempoTotalDia'] = $dataHelper->converteSegundosPorExtenso($totalTempoDia);
			}
		}
		//retorno
		return $arrRetorno;
    }
}

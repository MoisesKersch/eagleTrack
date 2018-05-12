<?php
namespace App\Services;

use DB;
use App\Helpers\DataHelper;
use App\Models\Bilhete;

class TempoFuncionamentoService
{
    //FUNCOES AQUI
    public function consulta($request)
    {
    	$dataHelper = new DataHelper;
    	$inicio = $request->data_inicio.' 00:00:00';
    	$fim = $request->data_fim.' 23:59:59';
        $bilhetes = Bilhete::select(DB::raw("vecodigo,
											 veplaca ||' | '|| veprefixo as placaprefixo,
											 bidataevento,
											 to_char(bidataevento, 'DD/MM/YYYY') as data,
											 to_char(bidataevento, 'HH24:MI') as hora,
											 biignicao,
											 coalesce(podescricao, ' ') as ponto,
											 bireferencia"
    										)
    								)
					    			->leftJoin('pontos','biponto','=','pocodigo')
					    			->leftJoin('veiculos','biplaca','=','veplaca')
					    			->whereBetween('bidataevento',[$inicio,$fim])
									->whereIn('vecodigo',$request->buscar)
									->orderBy('bidataevento')
									// ->limit(500)
									->get();
		$arrAgrupBilhetes = [];
		//agrupamento por prefixo|data|bilhetes
		foreach($bilhetes as $bilhete){
			$arrAgrupBilhetes[$bilhete->placaprefixo][$bilhete->data][] = $bilhete;
		}
		//tratamento de eventos
		$arrBilhetes = [];
		foreach($arrAgrupBilhetes as $p => $prefixo){
			foreach($prefixo as $d => $data){
				// dd($arrBilhetes[$prefixo]);
				$arrBilhetes[$p][$d]['tempoTotal'] = 0;
				$achouLigado = False;
				$ponto = null;
				$referencia = null;
				$dataIni = null;
				$horaIni = null;
				foreach($data as $bilhete){
					if($bilhete->biignicao == 1 && $achouLigado == False){
						// dd("ACHOU LIGADO");
						$dataIni = $bilhete->bidataevento;
						$horaIni = $bilhete->hora;
						$ponto = $bilhete->ponto;
						$referencia = $bilhete->bireferencia;
						$achouLigado = True;
					}
					if($bilhete->biignicao == 0 && $achouLigado == True){
						$tempoSegundos = $dataHelper->diferencaDatas($dataIni, $bilhete->bidataevento);
						$tempo = $dataHelper->converteSegundosPorExtenso($tempoSegundos);
						$arrBilhetes[$p][$d][] = array('inicio'     => $horaIni,
													   'fim'        => $bilhete->hora,
													   'tempo'      => $tempo,
													   'ponto'      => $ponto,
													   'referencia' => $referencia
													  );
						$arrBilhetes[$p][$d]['tempoTotal'] += $tempoSegundos;
						$achouLigado = False;
					}
				}
				$arrBilhetes[$p][$d]['tempoTotal'] = $dataHelper->converteSegundosPorExtenso($arrBilhetes[$p][$d]['tempoTotal']);
			}
		}
		//remover datas sem bilhetes
		// dd($arrBilhetes);
		foreach($arrBilhetes as $p => $prefixo){
			foreach($prefixo as $d => $data){
				if(count($data) < 2)
					unset($arrBilhetes[$p]);
			}
		}

		return $arrBilhetes;
    }
}

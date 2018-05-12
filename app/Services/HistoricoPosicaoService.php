<?php
namespace App\Services;
use DB;
use Auth;
use DateTime;
use App\Models\Bilhete;
use App\Helpers\DataHelper;
use App\Helpers\MapaHelper;

class HistoricoPosicaoService
{
    public function gerarHistoricoPosicoes($dataInicio,$dataFim,$codEmpresas,$codVeiculos,$codGrupos){
        $dataInicio = $dataInicio." 00:00";
        $dataFim = $dataFim." 23:59";
        $dataHelper = new DataHelper();
        $mapaHelper = new MapaHelper();
        //consulta principal
        $queryIgnicao = Bilhete::select(DB::raw("bilhetes.biignicao,
	                                             bilhetes.bidataevento as datahora,
                                            	 to_char(bilhetes.bidataevento,'DD/MM/YYYY HH24:MI:SS') as bidataevento,
                                            	 to_char(bilhetes.bidataevento,'DD/MM/YYYY') as data,
                                            	 bilhetes.biendereco,
                                                 bilhetes.bilatlog as latlon,
                                                 biplaca,
                                                 coalesce(motoristas.mtnome,'Sem Motorista') as mtnome,
                                                 bimotorista,
                                                 bihodometro/1000 as bihodometro,
                                                 to_char(bilhetes.bidataevento,'HH24:MI:SS') as hora,
                                                 coalesce(podescricao, ' ') as podescricao,
                                                 bimotivotransmissao,
                                                 biponto"
                                                )
                                        )
                               ->leftJoin('veiculos','veiculos.veplaca','=','bilhetes.biplaca')
                               ->leftJoin('pontos', 'pocodigo', '=', 'bilhetes.biponto')
                               ->leftJoin('motoristas','motoristas.mtcodigo','=','bilhetes.bimotorista')
                               ->whereIn('bimotivotransmissao', [9, 10, 4, 14, 16, 18, 20, 23, 25, 26])
                               ->whereBetween('bidataevento',[$dataInicio, $dataFim]);
                               if(!empty($codVeiculos))
                                   $queryIgnicao->whereIn('veiculos.vecodigo', $codVeiculos);
                               if(!empty($codGrupos))
                                   $queryIgnicao->whereIn('motoristas.mtgrupo', $codGrupos);
                               $queryIgnicao->orderBy('bidataevento');
       $queryIgnicao = $queryIgnicao->get();
        //agrupamento por placa/motorista, data
        $bilheteAgrup = [];
        foreach($queryIgnicao as $bilhete){
            $bilheteAgrup[$bilhete->biplaca . "|" . $bilhete->mtnome][$bilhete->data][] = $bilhete;
        }
        // dd($bilheteAgrup);
        //tratamento de eventos
        $arrFinal = [];
        foreach($bilheteAgrup as $p => $placa){
            foreach ($placa as $d => $data) {
                // dd($data);
                $achouLigado = False;
                $dataHoraDesligado;
                $contadores = (object) array(
                                            'parado12'    => 0,
                                            'paradoPonto' => 0,
                                            'madrugada'   => 0,
                                            'porta'       => 0,
                                            'km'          => 0
                                        );
                $hodometroIni = 0;
                $ultimaChave = 0;
                foreach ($data as $b => $bilhete) {
                    if($hodometroIni == 0)
                        $hodometroIni = $bilhete->bihodometro;
                    $end_cidade = $mapaHelper->quebraEnderecoCidadeUF($bilhete->biendereco);
                    //ignicao Desligada
                    if($bilhete->bimotivotransmissao == 10 && $achouLigado == True){
                        $achouLigado = False;
                        $dataHoraDesligado = $bilhete->bidataevento;
                        $arrFinal[$p][$d][] = (object) array(
                                                            'hora'     => $bilhete->hora,
                                                            'tempo'    => ' ',
                                                            'evento'   => 'ID',
                                                            'endereco' => $end_cidade[0],
                                                            'cidade'   => $end_cidade[1],
                                                            'ponto'    => $bilhete->podescricao,
                                                            'latlon'   => $bilhete->latlon
                                                             );
                    }
                    //ignicao ligada
                    if($bilhete->bimotivotransmissao == 9 && $achouLigado == False){
                        $achouLigado = True;
                        if(empty($dataHoraDesligado))
                            $dataHoraDesligado = $bilhete->data . " 00:00";
                        $totalTempoParado = $dataHelper->diferencaDatas($dataHoraDesligado, $bilhete->bidataevento);
                        $tempoExt = $dataHelper->converteSegundosPorExtenso($totalTempoParado);
                        //verifica se andou de madrugada
                        $meiaNoiteSeg = strtotime($bilhete->data . " 00:00");
                        $seisManhaSeg = strtotime($bilhete->data . " 06:00");
                        $horaBilheteSeg = strtotime($bilhete->bidataevento);
                        if($horaBilheteSeg >= $meiaNoiteSeg && $horaBilheteSeg <= $seisManhaSeg)
                            $contadores->madrugada += 1;
                        //se dentro de ponto
                        if( (int)$bilhete->biponto > 0){
                            $contadores->paradoPonto += 1;
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => $tempoExt,
                                                                'evento' => 'PP',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                        }
                        else{
                            //se tempo parado maior que 12 horas
                            if($totalTempoParado > 43200){
                                $contadores->parado12 += 1;
                                $arrFinal[$p][$d][] = (object) array(
                                                                    'hora' => $bilhete->hora,
                                                                    'tempo' => $tempoExt,
                                                                    'evento' => 'P12',
                                                                    'endereco' => $end_cidade[0],
                                                                    'cidade'   => $end_cidade[1],
                                                                    'ponto'    => $bilhete->podescricao,
                                                                    'latlon'   => $bilhete->latlon
                                                                     );
                            }
                            else{
                                $arrFinal[$p][$d][] = (object) array(
                                                                    'hora' => $bilhete->hora,
                                                                    'tempo' => $tempoExt,
                                                                    'evento' => 'TP',
                                                                    'endereco' => $end_cidade[0],
                                                                    'cidade'   => $end_cidade[1],
                                                                    'ponto'    => $bilhete->podescricao,
                                                                    'latlon'   => $bilhete->latlon
                                                                     );
                            }
                        }
                        $arrFinal[$p][$d][] = (object) array(
                                                            'hora' => $bilhete->hora,
                                                            'tempo' => ' ',
                                                            'evento' => 'IL',
                                                            'endereco' => $end_cidade[0],
                                                            'cidade'   => $end_cidade[1],
                                                            'ponto'    => $bilhete->podescricao,
                                                            'latlon'   => $bilhete->latlon
                                                             );
                    }
                    //Outros eventos
                    switch ($bilhete->bimotivotransmissao){
                        case 4://movimento
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'M',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => ' ',
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 14://porta 1
                            $contadores->porta += 1;
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'P1',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 16://porta 2
                            $contadores->porta += 1;
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'P2',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 18://porta 3
                            $contadores->porta += 1;
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'P3',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 20://porta 4
                            $contadores->porta += 1;
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'P4',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 23://antifurto ativado
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'FA',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 25://energia externa cortada
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'EC',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                        case 26://energia restaurada
                            $arrFinal[$p][$d][] = (object) array(
                                                                'hora' => $bilhete->hora,
                                                                'tempo' => ' ',
                                                                'evento' => 'ER',
                                                                'endereco' => $end_cidade[0],
                                                                'cidade'   => $end_cidade[1],
                                                                'ponto'    => $bilhete->podescricao,
                                                                'latlon'   => $bilhete->latlon
                                                                 );
                            break;
                    }
                    $ultimaChave = $b;
                }//fim foreach bilhetes
                $kmTotal = $bilheteAgrup[$p][$d][$ultimaChave]->bihodometro - $hodometroIni;
                $contadores->km = $kmTotal;
                $arrFinal[$p][$d]['contadores'] = $contadores;
            }
        }
        // dd($arrFinal);
        //remover excesso de eventos do tipo M
        foreach($arrFinal as $p => $placa){
            foreach($placa as $d => $data){
                $enderecoAnt = '';
                foreach($data as $b => $bilhete){
                    if(is_numeric($b)){
                        if($bilhete->evento == 'M'){
                            if($enderecoAnt == $bilhete->endereco)
                                unset($arrFinal[$p][$d][$b]);
                            else
                                $enderecoAnt = $bilhete->endereco;
                        }
                    }
                }
            }
        }
        //veririca se vazio
        if(count($arrFinal) == 0)
            $arrFinal = False;
        return $arrFinal;
    }
}

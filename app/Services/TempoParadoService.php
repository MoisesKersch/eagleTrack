<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Bilhete;
use App\Models\Pontos;
use DB;
use App\Helpers\DataHelper;
use App\Helpers\PdfHelper;
use Excel;

class TempoParadoService
{

    public function query($request)
    {
        $inp_time_parado = 60 * $request->inp_time_parado; //converter para segundos

        $idVeiculo = array();
        //tratamento veiculos
        if(!empty($request->buscar)){
            foreach($request->buscar as $id){
                $idVeiculo[] = (int)$id;
            }
        }
        //tratamento empresas
        $idCliente = array();
        if(!empty($request->clientes)){
            foreach ($request->clientes as $id) {
                $idCliente[] = (int)$id;
            }
        }else{
            $idCliente[] = \Auth::user()->usucliente;
        }
        //FILTRO IGNICAO
        $ignicao = array(0,1);
        if($request->ligado == 'on' && $request->desligado == 'on'){
            $ignicao = array(0,1);
        }
        if($request->ligado == 'off' && $request->desligado == 'on'){
            $ignicao = array(0);
        }
        if($request->ligado == 'on' && $request->desligado == 'off'){
            $ignicao = array(1);
        }
        $clientesbusca = $request->clientes;
        $dataInicio = "$request->data_inicio 00:00";
        $dataFim = "$request->data_fim 23:59";
        $tempoParadoSql = Bilhete::select(DB::raw("min(bidataevento) as bidataevento,
                                            	min(to_char(bidataevento,'DD/MM/YYYY')) as data,
                                            	min(to_char(bidataevento,'DD/MM/YYYY HH24:MI:SS')) as datahora,
                                            	min(to_char(bidataevento,'HH24:MI')) as hora,
                                            	biendereco,
                                                coalesce(podescricao, ' ') as ponto,
                                                coalesce(redescricao, ' ') as regiao,
                                            	biignicao,
                                            	bilatlog,
                                            	biplaca,
                                                veprefixo,
                                            	bimovimento,
                                                mtnome"))
                                ->leftJoin("veiculos","veplaca","=","biplaca")
                                ->leftJoin("motoristas", "bimotorista", "mtcodigo")
                                ->leftJoin("pontos", "pocodigo", "biponto")
                                ->leftJoin("regioes", "recodigo", "biregiao")
                                ->whereBetween("bidataevento",[$dataInicio,$dataFim])
                                ->whereIn('bimotivotransmissao', [3, 4, 9, 10, 21, 22, 69])
                                ->whereIn("biignicao", $ignicao)
                                ->whereIn("vecodigo", $idVeiculo)
                                ->groupBy("biendereco")
                                ->groupBy("podescricao")
                                ->groupBy("redescricao")
                                ->groupBy("biignicao")
                                ->groupBy("bilatlog")
                                ->groupBy("biplaca")
                                ->groupBy("bimovimento")
                                ->groupBy('mtnome')
                                ->groupBy("veprefixo")
                                ->orderBy("bidataevento");

        $tempoParadoSql = $tempoParadoSql->get();
        //verifica se consulta tem resultados
        if(count($tempoParadoSql) == 0){
            return false;
        }
        //agrupamento por placa e data
        $tempoParadoAgp;
        foreach($tempoParadoSql as $linha){
            if(empty($linha->mtnome))
                $linha->mtnome = 'Sem Motorista';
            $tempoParadoAgp[$linha->biplaca.' | '.$linha->veprefixo.' - '.$linha->mtnome][$linha->data][] = $linha;
        }
        //tratamento eventos parado
        $dataHelper = new DataHelper;
        $arrayAux;//armazena somente linhas com que contam soma de tempo e ponto proximo
        foreach($tempoParadoAgp as $placa => $linhaPlaca){
            foreach($linhaPlaca as $data => $linhaData){
                $pontoAnterior;
                $achouParado = false;
                $dataHoraAnterior = $dataInicio;
                $horaAnterior = "00:00:00";
                $tempoParado = 0;//segundos
                $totalizadorTempo = 0;//segundos
                foreach($linhaData as $key => $linha){
                    //tratamento movimento, parado.
                    if($linha->bimovimento == 0 && $achouParado == false){
                        $achouParado = true;
                        $dataHoraAnterior = $linha->datahora;
                        $horaAnterior = $linha->hora;
                        $pontoAnterior = $linha->ponto;
                    }
                    if($linha->bimovimento == 1 && $achouParado == true){
                        $achouParado = false;
                        $tempoParado = $dataHelper->diferencaDatas($dataHoraAnterior, $linha->datahora);
                        if($inp_time_parado > 0 && $tempoParado < $inp_time_parado){
                            continue;
                        }
                        //calcula tempo parada
                        $totalizadorTempo = $totalizadorTempo + $tempoParado;
                        $arrayAux[$placa][$data][$key] = $linha;
                        $arrayAux[$placa][$data][$key]['tempo'] = $dataHelper->converteSegundosPorExtenso($tempoParado);
                        $arrayAux[$placa][$data][$key]['dataInicio'] = $horaAnterior;
                        $arrayAux[$placa][$data][$key]['dataFim'] = $linha->hora;
                        $arrayAux[$placa][$data][$key]['ponto'] = $pontoAnterior;
                    }
                }
                $arrayAux[$placa][$data]['totalizadorTempo'] = $dataHelper->converteSegundosPorExtenso($totalizadorTempo);
            }
        }//fim foreach tempo parado

        //reindexar chaves
        $arrayRetorno;
        foreach($arrayAux as $placa=>$linhaPlaca){
            foreach($linhaPlaca as $data=>$linhaData){
                    foreach($linhaData as $key=>$linha){
                        if($key != 'totalizadorTempo'){
                            $arrayRetorno[$placa][$data][] = $linha;
                        }else{
                            if($arrayAux[$placa][$data]['totalizadorTempo'] != "Menos de 1 minuto.")
                                $arrayRetorno[$placa][$data]['totalizadorTempo'] = $arrayAux[$placa][$data]['totalizadorTempo'];
                        }
                    }
            }
        }
        return $arrayRetorno;
    }


    public function paradas(Request $request)
    {
        $placa = $request->placa;
        $dataI = $request->dataIni;
        $dataF = $request->dataFim;
        $dataHelper = new DataHelper;
        ######VARIAVEIS#######################
        $dados = array();
        $cont = 0;
        $tamanho = 0;
        $valido = false;

        $resultado = \DB::table('bilhetes as b')
            ->select(DB::raw("to_char(bidataevento,'DD/MM/YYYY HH24:MI:SS') as datahora,bimovimento,biendereco,bilatlog,biplaca"))
            ->where('biplaca', '=', $placa)
            ->whereBetween('bidataevento', [$dataI, $dataF])
            ->orderBy('datahora', 'ASC')
            ->get();

        $tamanho = (count($resultado))-1;

        $menor = 9999;
        while($cont < $tamanho){
            $inicio = null;
            $fim = null;
            $diferenca;
            if($valido == true) $menor = 9999;
            $valido = false;

            if($resultado[$cont]->bimovimento == 0){
                if($cont < $menor) $menor = $cont;
                $inicio = $resultado[$menor]->datahora;
                if($resultado[$cont + 1]->bimovimento == 1){
                    $valido = true;
                    $fim = $resultado[$cont + 1]->datahora;
                    $latlng = explode(",",$resultado[$menor]->bilatlog);
                }
            }
            if($valido){
                $diferenca = $dataHelper->diferencaDatas($inicio,$fim);
                $diferencaExt = $dataHelper->converteSegundosPorExtenso((int)$diferenca);
                $resumido = $this->montaDescPlacaParada($diferencaExt);
                $dados[] = array(
                                    "data" => $inicio,
                                    "segundos" => (int)$diferenca,
                                    "diferenca" => $diferencaExt,
                                    "endereco" => $resultado[$menor]->biendereco,
                                    "lat" => (float)$latlng[0],
                                    "lng" => (float)$latlng[1],
                                    "resumido" => $resumido,
                                    "placa" => $resultado[$cont]->biplaca
                                );
            }//fim if valido
            $cont++;
        }//fim while
        // dd($dados);

        return json_encode($dados);
    }

    private function montaDescPlacaParada($array)
    {
        $explodeDif = explode(' ', $array);
        $count = count($explodeDif);
        if ($count == 2) {
            return $explodeDif[0].'M';
        }
        if ($count == 4 && $explodeDif[0] == "Menos") {
            return '-1M';
        }
        if ($count == 4) {
            return '+'.$explodeDif[0].'H';
        }
        if ($count == 6) {
            return $explodeDif[0].'D';
        }
    }



}

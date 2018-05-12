<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use App\Models\Bilhete;
use App\Models\Pontos;
use DB;
use App\Models\Motorista;
use App\Helpers\MapaHelper;
use App\Helpers\DataHelper;
use App\Helpers\PontosHelper;
use App\Helpers\PdfHelper;
use Excel;

class ControleHorariosService
{

    public static function query($request)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $horarios = Bilhete::select('bidataevento', 'mtnome', 'bimotorista',
            'bimotivotransmissao', 'bilatlog', 'mtlatitude', 'mtlongitude',
            'mtcliente', 'biplaca','veprefixo',  'biendereco', 'bicodigo', 'vecodigo', 'bimotorista')
            ->join('motoristas', 'mtcodigo', '=', 'bimotorista')
            ->join('veiculos', 'veplaca', '=', 'biplaca')
            ->where('bidataevento', '>', $request->data_inicio . ' 00:00:00')
            ->where('bidataevento', '<', $request->data_fim . ' 23:59:59')
            ->whereIn('bimotivotransmissao', [9,10]);
            if(!empty($request->motoristas)){
                $horarios->whereIn('bimotorista', $request->motoristas);
            }
            if(!empty($request->veiculos)){
                $horarios->whereIn('vecodigo', $request->veiculos);
            }
            if(\Auth::user()->usumaster == 'N'){
                $horarios->join('usuarios_clientes', 'uclcliente', '=', 'mtcliente')
                ->where('uclusuario', '=', \Auth::user()->id);
            }
            $horarios->where('bimotorista', '<>', 0)
            ->orderBy('bidataevento', 'asc')
            ->orderBy('bimotorista', 'asc');
            $horarios = $horarios->get();
        $inicio = '';
        $old = '';
        $fim = '';
        $final = '';
        $trini = '';
        $tempo = '';
        $oldDia = '';

        if($horarios->isEmpty()){
            return response (['placa' => '']);
        }

        foreach($horarios as $i => $or) {
            $ini = new \DateTime($or->bidataevento);
            $pt = new PontosHelper;
            $latlog = explode(',', $or->bilatlog);
            $final = isset($horarios[$i +1]) ? $horarios[$i + 1]->bimotorista : '';
            $finalDia = isset($horarios[$i +1]) ? $horarios[$i + 1]->bidataevento : '';

            if($or->bimotorista != $old){
                if($or->bimotivotransmissao == 10) {
                    $ini = new \DateTime(substr($or->bidataevento, 0, 11) . '00:00:00');
                    $fi = new \DateTime($horarios[$i +1]->bidataevento);

                    $placa[$or->biplaca][$ini->format('Y-m-d')]['ini'][$i] = $ini->format('d/m H:i');
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['fi'][$i] = ''; //Remover "nada consta", deixar em branco, pois acaba confundindo
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['tempo'][$i] = ''; //Remover "nada consta", deixar em branco, pois acaba confundindo
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['endereco'][$i] = $or->biendereco;
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['ponto'][$i] = $pt->buscaPontoProximo($latlog[0], $latlog[1], 0, $or->mtcliente);
                    $placa[$or->biplaca]['motorista'] = $or->mtnome;
                    $placa[$or->biplaca]['prefixo'] = $or->veprefixo;
                    $or->bidataevento = substr($or->bidataevento, 0, 11) . '00:00';
                }else{
                    $ini = new \DateTime($or->bidataevento);
                    $fi = new \DateTime($horarios[$i + 1]->bidataevento);

                    $placa[$or->biplaca][$ini->format('Y-m-d')]['ini'][$i] = $ini->format('d/m H:i');
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['fi'][$i] = ''; //Remover "nada consta", deixar em branco, pois acaba confundindo
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['tempo'][$i] = ''; //Remover "nada consta", deixar em branco, pois acaba confundindo
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['endereco'][$i] = $or->biendereco;
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['ponto'][$i] = $pt->buscaPontoProximo($latlog[0], $latlog[1], 0, $or->mtcliente);
                    $placa[$or->biplaca]['motorista'] = $or->mtnome;
                    $placa[$or->biplaca]['prefixo'] = $or->veprefixo;
                }
                $fim = new \DateTime($horarios[$i +1]->bidataevento);
                $trini[] = $or->bimotivotransmissao;
            }else {
                if(isset($horarios[$i + 1]) && $or->bimotivotransmissao == 10 && substr($or->bidataevento, 0, 11) == substr($horarios[$i + 1]->bidataevento, 0, 11)) {
                    $ini = new \DateTime($or->bidataevento);
                    $fi = new \DateTime($horarios[$i + 1]->bidataevento);

                    $placa[$or->biplaca][$ini->format('Y-m-d')]['ini'][$i] = $ini->format('d/m H:i');
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['fi'][$i] = $fi->format('d/m H:i');
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['tempo'][$i] = $ini->diff($fi)->format('%H:%I:%S');
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['endereco'][$i] = $or->biendereco;
                    $placa[$or->biplaca][$ini->format('Y-m-d')]['ponto'][$i] = $pt->buscaPontoProximo($latlog[0], $latlog[1], 0, $or->mtcliente);
                    $placa[$or->biplaca]['motorista'] = $or->mtnome;
                    $placa[$or->biplaca]['prefixo'] = $or->veprefixo;
                }
            }

            if($fi->format('Y-m-d') != $ini->format('Y-m-d') || $or->bimotorista != $final) {
                $fim = new \DateTime($or->bidataevento);
                $placa[$or->biplaca][$ini->format('Y-m-d')]['ini'][$i] = ''; //Remover "nada consta", deixar em branco, pois acaba confundindo
                $placa[$or->biplaca][$ini->format('Y-m-d')]['fi'][$i] = $fim->format('d/m H:i');
                $placa[$or->biplaca][$ini->format('Y-m-d')]['tempo'][$i] = ''; //Remover "nada consta", deixar em branco, pois acaba confundindo
                $placa[$or->biplaca][$ini->format('Y-m-d')]['endereco'][$i] = $or->biendereco;
                $placa[$or->biplaca][$ini->format('Y-m-d')]['ponto'][$i] = $pt->buscaPontoProximo($latlog[0], $latlog[1], 0, $or->mtcliente);
                if($or->bimotivotransmissao == 9) {
                    $or->bidataevento = substr($or->bidataevento, 0, 11) . '23:59:59';
                }
            }
            $old = $or->bimotorista;
            $oldDia = $ini->format('Y-m-d H:i');
        }

        return $placa;
    }

}

<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use DB;
use Excel;

class RotaService
{

    public function query(Request $request)
    {
        $codigo_rota = $request->codigo_rota;
        $data = $request->data;
        $dados = null;

        $usuario = \Auth::user();
        if($data != null || $codigo_rota != null){
            $dados = DB::table('rotas')
                ->select('rocodigo','irrota','podescricao','irplaca','irdata','ircubagem','irpeso','irqtde','irordem','ircodigoexterno')
                ->addSelect('*')
                ->addSelect(DB::raw("(select podescricao from pontos where pocodigo = ropontosaida) as ropontosaida"))
                ->addSelect(DB::raw("(select podescricao from pontos where pocodigo = ropontoretorno) as ropontoretorno"))
                ->join('itens_rotas','irrota','=','rocodigo')
                ->join('pontos','pocodigoexterno','=','ircodigoexterno')
                ->whereIn('pocodigocliente',$usuario->getEmpresasUsuario())
                ->whereIn('rocliente', $usuario->getEmpresasUsuario());
                if($data != null){
                    $date = \DateTime::createFromFormat('d/m/Y', $data);
                    $data = $date->format('Y-m-d');
                    $dados = $dados->where('irdata','=',$data);
                }
                if($codigo_rota != null){
                    if(is_array($codigo_rota)){
                        $dados = $dados->whereIn('irrota',$codigo_rota);
                    }else{
                        $dados = $dados->where('irrota','=',$codigo_rota);
                    }
                }
                $dados = $dados->orderBy('roplaca','ASC');
                $dados = $dados->orderBy('rocodigo','ASC');
                $dados = $dados->orderBy('irordem','ASC');
                $dados = $dados->get();
                $dados = $this->agruparRota($dados);
        }

        return $dados;
    }


    public function agruparRota($dados){

        $rotas = null;
        $old = '';
        $j = 0;
        $i = 0;

        foreach($dados as $reg) {
            if($i == 0 || $reg->rocodigo != $old_rota){
                $j = 0;
                $old_rota = $reg->rocodigo;
            }
            if($reg->rocodigo == $old_rota) {
                $rotas[$reg->rocodigo]['roplaca'] = $reg->roplaca;
                $rotas[$reg->rocodigo]['ropontosaida'] = $reg->ropontosaida;
                $rotas[$reg->rocodigo]['ropontoretorno'] = $reg->ropontoretorno;

                $rotas[$reg->rocodigo][$j]['irrota'] = $reg->irrota;
                $rotas[$reg->rocodigo][$j]['podescricao'] = $reg->podescricao;
                $rotas[$reg->rocodigo][$j]['irplaca'] = $reg->irplaca;
                $rotas[$reg->rocodigo][$j]['irdata'] = $reg->irdata;
                $rotas[$reg->rocodigo][$j]['ircubagem'] = $reg->ircubagem;
                $rotas[$reg->rocodigo][$j]['irpeso'] = $reg->irpeso;
                $rotas[$reg->rocodigo][$j]['irqtde'] = $reg->irqtde;
                $rotas[$reg->rocodigo][$j]['irordem'] = $reg->irordem;
                $j = $j + 1;
            }else{
                $j = 0;
            }
            $old_rota = $reg->rocodigo;
            $i = $i + 1;
        }
        return $rotas;
    }


}

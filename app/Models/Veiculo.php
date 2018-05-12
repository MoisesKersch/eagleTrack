<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Veiculo extends Model
{
    protected $primaryKey = 'vecodigo';
    protected $fillable = [
        "veprefixo",
        "veplaca",
        "vechassi",
        "vemaxpeso",
        "vecubagem",
        "veautonomia",
        "vemaxentregas",
        "vemaxhoras",
        "vecusto",
        "veproprietario",
        "vemodulo",
        "vemotorista",
        "veajudante",
        "vevelocidademax",
        "vedescricao",
        "vestatus",
        "vehorainiciotrabalho",
        "vehorafinaltrabalho",
        "vetipo",
        "veestradaterra",
        "vebalsas",
        "vepedagios",
        "veroterizar",
        "vegrupoveiculo"
    ];

    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente', 'veproprietario', 'clcodigo');
    }

    public function modulo()
    {
        return $this->belongsTo('App\Models\Modulo', 'vemodulo', 'mocodigo');
    }

    public function regioes()
    {
        return $this->belongsToMany('App\Models\Regioes', 'veiculo_regiao', 'vrveiculo', 'vrregiao');
    }

    public function grupo()
    {
        return $this->belongsTo('App\Models\GrupoVeiculo', 'vegrupoveiculo', 'gvcodigo');
    }

    public function perfis(){
        return $this->belongsToMany('App\Models\Perfil', 'perfil_veiculo', 'vecodigo', 'vecodigo');
    }

    /*
    *   Retorna total de veiculos obedecendo o usuario
    *   Se Master, todos ativos, senão, somente veiculos do cliente
    */
    public function totalVeiculos(){
        $usuario = Auth::user();
        if($usuario->usumaster == 'S'){
            $totalVeiculos = $this->where('vestatus','=','A')
                                  ->count();
        }else{
            $totalVeiculos = $this->where('vestatus','=','A')
                                  ->whereIn('veproprietario',$usuario->getEmpresasUsuario())
                                  ->count();
        }
        return $totalVeiculos;
    }
    /*
    *   Metodo retorna total de kms rodados no dia,
    *   passar como parametro o top veiculos, Ex: 10, para os 10 mais
    */
    public function rankingKms($total){
        $usuario = Auth::user();
        $ranking;
        $dataI = date("d/m/Y 00:00");
        $dataF = date("d/m/Y 23:59");
        if($usuario->usumaster == 'S'){
            $ranking = DB::table('bilhetes')
                            ->leftJoin('veiculos','bilhetes.biplaca','=','veiculos.veplaca')
                            ->select(DB::raw('coalesce((max(bihodometro) - min(bihodometro))/1000,0) as total,biplaca'))
                            ->where('veiculos.vestatus','=','A')
                            ->whereBetween('bidataevento',[$dataI,$dataF])
                            ->groupBy('biplaca')
                            ->orderBy('total','desc')
                            ->limit($total)
                            ->get();
            return $ranking;
        }else{
            $ranking = DB::table('bilhetes')
                            ->leftJoin('veiculos','bilhetes.biplaca','=','veiculos.veplaca')
                            ->select(DB::raw('coalesce((max(bihodometro) - min(bihodometro))/1000,0) as total,biplaca'))
                            ->where('veiculos.vestatus','=','A')
                            ->whereIn('veiculos.veproprietario',$usuario->getEmpresasUsuario())
                            ->whereBetween('bidataevento',[$dataI,$dataF])
                            ->groupBy('biplaca')
                            ->orderBy('total','desc')
                            ->limit($total)
                            ->get();
            return $ranking;
        }
    }

    /*
    *   Retorna lista de veiculos
    *   Pode receber um array de códigos, caso nao receba parametros, retorna lista de veiculos da empresa principal associada ao usuário
    */
    public function getVeiculos($proprietario){
        $retorno;
        if(!empty($proprietario)){
            $retorno = Veiculo::whereIn('veproprietario',$proprietario)
                              ->where('vestatus','=','A')
                              ->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])
                              ->get();
        }else{
            if(Auth::user()->usumaster == 'S'){
                $retorno = Veiculo::where('vestatus','=','A')->get();
            }else{
                $retorno = Veiculo::where('vestatus','=','A')
                                  ->where('veproprietario','=',Auth::user()->usucliente)
                                  ->whereNotIn('vecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])
                                  ->get();
            }
        }
        return $retorno;
    }
}

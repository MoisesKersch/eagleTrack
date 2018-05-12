<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class GrupoMotorista extends Model
{

    protected $table = "grupo_motorista";
    protected $primaryKey = "gmcodigo";
    public $timestamps = false;

    protected $fillable = [
      "gmcodigo",
      "gmdescricao",
      "gmcliente",
      "gmstatus"
    ];

    public function clienteGm()
    {
        return $this->belongsTo('App\Models\Cliente', 'gmcliente', 'clcodigo');
    }

    public function getGrpMotorista($proprietario){
        $retorno;
        if(!empty($proprietario)){
            $retorno = GrupoMotorista::whereIn('gmcliente',$proprietario)
                                     ->where('gmstatus','=','A')
                                     ->get();
        }else{
            if(Auth::user()->usumaster == 'S'){
                $retorno = GrupoMotorista::where('gmstatus','=','A')
                                         ->get();
            }else{
                $retorno = GrupoMotorista::where('gmstatus','=','A')
                                         ->where('gmcliente','=',Auth::user()->usucliente)
                                         ->get();
            }
        }
        return $retorno;
    }

}

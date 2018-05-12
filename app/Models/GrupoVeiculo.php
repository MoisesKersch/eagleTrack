<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class GrupoVeiculo extends Model
{

    protected $table = "grupo_veiculos";
    protected $primaryKey = "gvcodigo";
    // public $timestamps = false;

    protected $fillable = [
      "gvcodigo",
      "gvdescricao",
      "gvempresa",
      "gvstatus"
    ];

    public function clienteGv()
    {
        return $this->belongsTo('App\Models\Cliente', 'gvempresa', 'clcodigo');
    }

    public function veiculos()
    {
        return $this->hasMany('App\Models\Veiculo', 'vegrupoveiculo', 'gvcodigo');
    }

    //array de codigos dos veiculos associados e este grupo
    public function codigosVeiculos(){
        $veiculos = Veiculo::select('vecodigo')->where('vegrupoveiculo',$this->gvcodigo)->get();
        $arr = array();

        foreach ($veiculos as $key => $value) {
            array_push($arr, $value->vecodigo);
        }

        return $arr;
    }

}

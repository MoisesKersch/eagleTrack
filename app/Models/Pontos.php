<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pontos extends Model
{
  protected $primaryKey = "pocodigo";

  protected $fillable = [
            "pocodigo",
            "pocodigoexterno",
            "podescricao",
            "pocodigocliente",
            "potipo",
            "polatitude",
            "polongitude",
            "poendereco",
            "poraio",
            "poregiao",
            "created_at",
            "updated_at"
            ];


    public function motoristaPonto()
    {
        return $this->belongsTo('App\Models\MotoristaPonto', 'mpponto', 'pocodigo');
    }
    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente', 'pocodigocliente', 'clcodigo');
    }
    public function disponibilidade()
    {
        return $this->hasMany('App\Models\PontosDisponibilidade', 'pdicodigoponto', 'pocodigo');
    }
    public function regiao()
    {
        return $this->belongsTo('App\Models\Regioes', 'poregiao', 'recodigo');
    }

    public function itemRota()
    {
        return $this->belongsTo('App\Models\itemRota', 'ircodigoexterno', 'pocodigoexterno');
    }

    public function getPontos($codEmpresas)
    {
        $retorno = new Pontos();
        if(!empty($codEmpresas)){
            $retorno = $retorno->whereIn('pocodigocliente',$codEmpresas);
        }
        return $retorno->get();
    }

    public static function getPontosCargas($codigo, $cliente)
    {
        $query = Pontos::where('pocodigoexterno', '=', $codigo)
            ->where('pocodigocliente', '=', $cliente)
            ->first();
        return $query;
    }

}

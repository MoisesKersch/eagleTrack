<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegioesCoordenadas extends Model
{
    protected $primaryKey = "rccodigo";
    protected $table = 'regioes_coordenadas';
    public $timestamps = false;
    protected $fillable = [
        "recodigo",
        "rclatitude",
        "rclongitude",
        "rcregiao"
    ];

    public function regiao()
    {
        return $this->BelongsTo('App\Models\Regioes', 'rcregiao', 'recodigo');
    }

    public function salvarCoordenadasRegiao($dados, $idRegiao)
    {
        foreach ($dados as $key => $dado) {
            $coordRegiao = new RegioesCoordenadas();
            $coordRegiao->rclatitude = $dado['lat'];
            $coordRegiao->rclongitude = $dado['lng'];
            $coordRegiao->rcregiao = $idRegiao;
            $coordRegiao->save();
        }
    }
}

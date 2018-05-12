<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PontoLinha extends Model
{
    protected $primaryKey = "plcodigo";
    protected $table = 'pontos_linhas';

    protected $fillable = [
        "pllicodigo",
        "plpocodigo",
        "plpoordem",
        "pltempoestimado"
    ];

    public function linha()
    {
        return $this->belongsTo('App\Models\Linha','licodigo', 'pllicodigo');
    }

    public function ponto()
    {
        return $this->hasOne('App\Models\Pontos','plpocodigo','pocodigo');
    }
}

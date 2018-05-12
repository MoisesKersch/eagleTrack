<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoRegiao extends Model
{
    protected $primaryKey = "hrcodigo";
    protected $table = 'historico_regiao';
    public $timestamps = false;

    protected $fillable = [
        "hrcodigo",
        "hrregiao",
        "hrbilhete",
        "hrplaca"
    ];

    public function veiculo()
    {
        return $this->hasOne('App\Models\Veiculo', 'veplaca', 'hrplaca');
    }

    public function bilhete()
    {
        return $this->hasOne('App\Models\Bilhete', 'bicodigo', 'hrbilhete');
    }

    public function regiao()
    {
        return $this->hasOne('App\Models\Regioes','recodigo','hrregiao');
    }

}

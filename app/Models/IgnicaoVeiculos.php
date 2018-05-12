<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgnicaoVeiculos extends Model
{
    protected $primaryKey = "ivcodigo";
    public $timestamps = false;

    protected $fillable = [
      "ivcodigo",
      "ivdataevento",
      "ivplaca",
      "ivmotorista",
      "ivmotivotransmissao",
      "ivcliente",
      "ivponto"
    ];

    public function cliente()
    {
      return $this->belongsTo('App\Models\Cliente', 'ivcliente', 'clcodigo');
    }
}

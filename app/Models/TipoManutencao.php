<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoManutencao extends Model
{
    protected $primaryKey = "ticodigo";
    protected $table = 'tipo_manutencoes';

    protected $fillable = [
              "ticodigo",
              "timdescricao",
              "timkmpadrao",
              "timproprietario"
              ];


      public function cliente()
      {
          return $this->belongsTo('App\Models\Cliente', 'timproprietario', 'clcodigo');
      }
}

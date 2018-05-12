<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManutencaoProgramada extends Model
{
    protected $primaryKey = "macodigo";

    protected $fillable = [
              "macodigo",
              "mapcodigoveiculo",
              "mapkmprogramado",
              "mapdatahoralancamento",
              "mapusuario",
              "maptipomanutencao",
              "mapstatus",
              "mapkmrealizado"
              ];


      public function veiculo()
      {
          return $this->belongsTo('App\Models\Veiculo', 'mapcodigoveiculo', 'vecodigo');
      }

      public function cliente()
      {
          return $this->belongsTo('App\Models\Cliente', 'mapcliente', 'clcodigo');
      }

      public function usuario()
      {
          return $this->belongsTo('App\Users', 'mapusuario', 'id');
      }

      public function tipoManutencao()
      {
          return $this->belongsTo('App\Models\TipoManutencao', 'maptipomanutencao', 'ticodigo');
      }
}

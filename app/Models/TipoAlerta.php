<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAlerta extends Model
{
  protected $primaryKey = "tacodigo";

  protected $fillable = [
     "tadescricao",
     "tasirene",
     "tabloqueio",
     "taentrada1",
     "taentrada2",
     "taentrada3",
     "taentrada4",
     "tasaida1",
     "tasaida2",
     "tasaida3",
     "tasaida4",
     "taicone",
     "created_at",
     "updated_at"
  ];
}

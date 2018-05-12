<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{

  protected $primaryKey = "alcodigo";

  protected $fillable = [
      "alcodigo",
      "alcliente",
      "almodulo",
      "alinfoalerta",
      "alstatus",
      "aldatahora",
      "created_at",
      "updated_at"
  ];
}

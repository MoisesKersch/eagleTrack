<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEventoApp extends Model
{

  protected $primaryKey = "id";

  protected $fillable = [
      "tecodigo",
      "tedescricao",
      "created_at",
      "updated_at"
  ];
}

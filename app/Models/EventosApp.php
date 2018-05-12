<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventosApp extends Model
{

  protected $table = "eventos_app";

  protected $primaryKey = "eacodigo";

  protected $fillable = [
      "eacodigo",
      "eatipoevento",
      "eacodigomotorista",
      "eadataevento",
      "eadataprocessamento",
      "eaparametro",
      "ealatitude",
      "ealongitude",
      "created_at",
      "updated_at"
  ];
}

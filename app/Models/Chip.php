<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chip extends Model
{

  protected $primaryKey = "chcodigo";

  protected $fillable = [
              "chcodigo",
              "chnumero",
              "iccid",
              "choperadora",
              "chfranquiamb",
              "chfranquiasms",
              "chcusto",
              "chstatus",
              "created_at",
              "updated_at"
            ];

  public function modulo()
   {
       return $this->belongsTo('App\Models\Modulo', 'chcodigo', 'mosim');
   }
}

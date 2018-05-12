<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuloModelo extends Model
{
	protected $table = "modulo_modelos";
	protected $primaryKey = "mmcodigo";

	protected $fillable = [
		"mmcodigo",
		"mmdescricao",

	];

	public function modulo()
    {

      return $this->hasMany('App\Models\Modulo', 'momodelo', 'mmcodigo');
    }
}

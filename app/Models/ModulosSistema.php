<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulosSistema extends Model
{
	protected $table = "modulos_sistema";
	protected $primaryKey = "mscodigo";

	protected $fillable = [
		"msdescricao",

	];

	public function clientes()
    {
        return $this->belongsToMany('App\Models\Clientes', 'modulos_sistema_cliente', 'mscmodulossistema', 'msccliente');
    }
}

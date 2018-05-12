<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licenca extends Model
{
    protected $primaryKey = 'licodigo';
    protected $fillable = [
		'lidescricao',
		'licliente'
    ];

    public function motorista()
    {
    	return $this->belongsToMany('App\Models\Motoristas', 'motorista_licencas', 'mlmotorista', 'mllicenca');
    }
}

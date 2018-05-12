<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
	protected $primaryKey = 'frcodigo';
    protected $fillable = [
    	'frdescricao',
		'frdata',
		'frcliente',
		'frtipo',
    ];

    public function cliente()
    {
    	return $this->belongsTo('App\Models\Cliente', 'frcliente', 'clcodigo');
    }
}

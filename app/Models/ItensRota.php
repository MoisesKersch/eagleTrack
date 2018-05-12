<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItensRota extends Model
{
    protected $table = 'itens_rotas';
    protected $primaryKey = 'ircodigo';
    protected $fillable = [
    	'ircliente',
		'ircodigoexterno',
		'irdocumento',
		'irnome',
		'irdata',
		'irqtde',
		'ircubagem',
		'irpeso',
		'irvalor',
		'irplaca',
		'irstatus',
		'irrota',
    ];

     public function rota()
     {
         return $this->belongsTo('App\Models\Rota', 'irrota', 'rocodigo');
     }

     public function ponto()
     {
         return $this->hasOne('App\Models\Pontos', 'pocodigoexterno', 'ircodigoexterno');
     }

};

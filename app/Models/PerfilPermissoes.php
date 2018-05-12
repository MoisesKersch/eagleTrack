<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilPermissoes extends Model
{
    protected $primaryKey = 'ppcodigo';
    protected $fillable = [
    	'ppvisualizar',
		'ppcadastrar',
		'ppeditar',
		'ppexcluir',
		'ppimportar',
		'ppperfilitens',
		'ppperfilcodigo'
    ];

    public function Perfil()
    {
        return $this->belongsTo('App\Models\Perfil', 'ppperfilcodigo', 'pecodigo');
    }

    public function itens()
    {
        return $this->belongsTo('App\Models\PerfilItens', 'ppperfilitens', 'picodigo');
    }

}

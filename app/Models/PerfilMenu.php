<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilMenu extends Model
{
    protected $primaryKey = 'pmcodigo';
    protected $fillable = [
    	'pmdescricao',
    ];

    public function itens()
    {
        return $this->hasMany('App\Models\PerfilItens', 'piperfilmenu', 'pmcodigo');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilItens extends Model
{
    protected $primaryKey = 'picodigo';
    protected $fillable = [
    	'pidescricao',
    	'piperfilmenu'
    ];


     public function menu()
     {
         return $this->belongsTo('App\Models\PerfilMenu','piperfilmenu' ,'pmcodigo');
     }

     public function permissoes()
     {
     	return $this->hasMany('App\Models\PerfilItens', 'ppperfilitens', 'picodigo');
     }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $primaryKey = 'escodigo';
    protected $fillable = ['esnome'];

    public function cidades()
    {
        return $this->hasMany('App\Models\Cidade', 'ciestato', 'escodigo');
    }
}

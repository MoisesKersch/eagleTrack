<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $primaryKey = 'cicodigo';
    protected $fillable = ['cinome', 'ciestado'];

    public function clientes()
    {
        return $this->hasMany('App\Models\Cliente', 'clcidade', 'cicodigo');
    }
    public function estado()
    {
        return $this->belongsTo('App\Models\Estado', 'ciestado', 'escodigo');
    }
}

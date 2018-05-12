<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefones extends Model
{
    protected $primaryKey = 'tlcodigo';
    protected $fillable = ['tlnumero', 'tlproprietario'];

    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente', 'tlproprietario', 'clcodigo');
    }
}

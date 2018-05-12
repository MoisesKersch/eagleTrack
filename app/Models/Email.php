<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $primaryKey = 'emcodigo';
    protected $fillable = ['ememail', 'emproprietario'];

    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente', 'emproprietario', 'clcodigo');
    }
}

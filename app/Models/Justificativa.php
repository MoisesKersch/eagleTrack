<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Justificativa extends Model
{
    protected $primaryKey = 'jucodigo';
    protected $fillable = [
    	'judescricao',
    	'jucliente',
    	'jucodigo'
    ];

    
}

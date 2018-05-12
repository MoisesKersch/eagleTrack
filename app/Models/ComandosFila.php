<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComandosFila extends Model
{
    protected $primaryKey = 'cfcodigo';
    protected $table = 'comandos_fila';
    protected $fillable = [
    	'cfmodelorastreador',
    	'cfparametro',
    	'cfmodulo',
    	'cfcomando',
    	'cfstatus',
    ];
}

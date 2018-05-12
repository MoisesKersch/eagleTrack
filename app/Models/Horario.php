<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $primaryKey = "hrcodigo";

    protected $fillable = [
        'hrhorario',
        'hrdiasemana',
        'hrlicodigo'
    ];

}

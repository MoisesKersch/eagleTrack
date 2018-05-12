<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechamentoFolha extends Model
{
    protected $primaryKey = 'fecodigo';
    protected $table = 'fechamento_folhas';
    protected $fillable = [
        'fehoratrabalhada',
        'fedataentrada',
        'fefimexpediente',
        'femotorista',
        'fedsr',
        'fehoraextra',
        'feextranoturno',
        'fehorafalta',
        'fehoraespera',
    ];
}

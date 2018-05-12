<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PontosDisponibilidade extends Model
{
    protected $table = 'pontos_disponibilidade';
    protected $primaryKey = 'pdicodigo';
    protected $fillable = ['pdihorainicio', 'pdihorafim', 'pdidiasemana', 'pdicodigoponto'];

    public function pontos()
    {
    	return $this->bilongsTo('App\Models\Ponto', 'pocodigo', 'pdicodigoponto');
    }
}

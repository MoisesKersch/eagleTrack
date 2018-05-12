<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorasJornadaTrabalho extends Model
{
    protected $primaryKey = "hjtcodigo";
    protected $table = 'horas_jornada_trabalho';

    protected $fillable = [
        "hjtcodigo",
        "hjtiniprimeirot",
        "hjtfimprimeirot",
        "hjtinisegundot",
        "hjtfimsegundot",
        "hjtdiasemana",
        "created_at",
        "updated_at"
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\HorasJornadaTrabalho;
use App\Models\Motorista;

class JornadaTrabalho extends Model
{
    protected $primaryKey = "jtcodigo";
    protected $table = 'jornada_trabalho';

    protected $fillable = [
        "jtdescricao",
        "jtstatus",
        "jtcliente",
        "jttipo",
        "jtdsr",
        "created_at",
        "updated_at"
    ];

    public function horasJornadaTrabalho()
    {
        return $this->hasMany('App\Models\HorasJornadaTrabalho', 'hjtjornada');
    }

    public function clienteJornada()
    {
        return $this->belongsTo('App\Models\Cliente', 'jtcliente', 'clcodigo');
    }

    public function sum_time()
    {
        $i = 0;
        foreach (func_get_args() as $time)
        {
            sscanf($time, '%d:%d', $hour, $min);
            $i += $hour * 60 + $min;
        }
        if ($h = floor($i / 60)) {
        $i %= 60;
        }
        return sprintf('%02d:%02d', $h, $i);
    }

    public function getTotalHorasMensais()
    {
        $horas = HorasJornadaTrabalho::where('hjtjornada', $this->jtcodigo)->get();

        $primeiroTurno = 0;
        $segundoTurno = 0;

        foreach($horas as $value)
        {
          $primeiroTurno+= (float)str_replace(":", "",$value->hjtfimprimeirot) - (float)str_replace(":", "",$value->hjtiniprimeirot);
          $segundoTurno+= (float)str_replace(":", "",$value->hjtfimsegundot) - (float)str_replace(":", "",$value->hjtinisegundot);
        }

        dd(sum_time('01:05', '00:02', '05:59'));

    }

}

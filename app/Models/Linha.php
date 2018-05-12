<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linha extends Model
{
    protected $table = "linhas";
    protected $primaryKey = 'licodigo';
    protected $fillable = [
    	'lidescricao',
        'livelocidademedia',
        'lidistancia',
        'litempoestimado',
        'liseguirordeminsercao',
        'licliente',
        'lirotaosrm',
    ];

    public function cliente()
    {
        //belongs == pertence;
        return $this->belongsTo('App\Models\Cliente', 'licliente', 'clcodigo');
    }
    public function pontosLinha()
    {
        return $this->hasMany('App\Models\PontoLinha', 'pllicodigo', 'licodigo');
    }
    public function horarios()
    {
        //belongs == pertence;
        return $this->hasMany('App\Models\Horario', 'hrlicodigo', 'licodigo');
    }

}

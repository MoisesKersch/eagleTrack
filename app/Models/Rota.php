<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    protected $table = 'rotas';
    protected $primaryKey = 'rocodigo';
    protected $fillable = [
        'rocodigo',
        'rodata',
        'ropontosaida',
        'rodatahorainicio',
        'ropontoretorno',
        'rodatahorafim',
        'rostatus',
        'roplaca',
        'rohodometroinicio',
        'rohodometrofim',
        'rocubagem',
        'roqtde',
        'rocliente',
        'ropeso',
        'rovalor',
        'rocor',
        'rotempo',
        'rocusto',
        'rokm',
        'created_at',
        'updated_at'
    ];

    public function cliente()
    {
        return $this->hasOne('App\Models\Cliente', 'clcodigo', 'rocliente');
    }

    public function pontoSaida()
    {
        return $this->hasOne('App\Models\Pontos', 'pocodigo', 'ropontosaida');
    }

    public function pontoRetorno()
    {
        return $this->hasOne('App\Models\Pontos', 'pocodigo', 'ropontoretorno');
    }

    public function itensRota()
    {
        return $this->hasMany('App\Models\ItensRota', 'irrota', 'rocodigo');
    }

    public function veiculo()
    {
        return $this->hasOne('App\Models\Veiculo', 'veplaca', 'roplaca');
    }
};

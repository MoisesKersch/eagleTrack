<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = "perfis";
    protected $primaryKey = 'pecodigo';
    protected $fillable = [
    	'pedescricao',
        'pecliente',
        'pestatus',
        'peshowallveiculos'
    ];

    public function veiculos()
    {
        return $this->belongsToMany('App\Models\Veiculo','perfil_veiculo', 'pvpecodigo', 'pvvecodigo');
    }

    public function veiculos_codigos()
    {
        return $this->belongsToMany('App\Models\Veiculo','perfil_veiculo', 'pvpecodigo', 'pvvecodigo')->select('vecodigo');
    }

    public function empresa()
    {
        //belongs == pertence;
        return $this->belongsTo('App\Models\Cliente', 'pecliente', 'clcodigo');
    }

    public function permissoes()
    {
        return $this->hasMany('App\Models\PerfilPermissoes', 'ppperfilcodigo', 'pecodigo');
    }

    public function permissoesPiId()
    {
        return $this->hasMany('App\Models\PerfilPermissoes', 'ppperfilcodigo', 'pecodigo');
    }

    // @return = Array ids;
    public function veiculosNegados(){
        $veiculosNegados = array();
        $negs = PerfilVeiculo::select('pvvecodigo')->where('pvpecodigo', \Auth::user()->perfil->pecodigo)->get();
        foreach ($negs as $negacao) {
            array_push($veiculosNegados,$negacao->pvvecodigo);
        }
        return $veiculosNegados;
    }

}

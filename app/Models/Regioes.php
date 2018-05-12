<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regioes extends Model
{
    protected $primaryKey = "recodigo";
    protected $table = 'regioes';
    protected $fillable = [
        "recodigo",
        "redescricao",
        "revelocidade",
        "recor",
        "recliente",
        "created_at",
        "updated_at"
    ];

    public function regioesCoordenadas()
    {
        return $this->hasMany('App\Models\RegioesCoordenadas', 'rcregiao', 'recodigo');
    }

    public function clienteRegioes()
    {
        return $this->belongsTo('App\Models\Cliente', 'recliente', 'clcodigo');
    }

    // public function salvarRegiao($dados)
    // {
        // $regiao = new Regioes();
        // $regiao->redescricao = $dados['descricao'];
        // $regiao->revelocidade = $dados['velocidade'];
        // $regiao->recliente = $dados['cliente'];
        // $regiao->recor = $dados['cor'];
        // $regiao->revelocidade = $dados['velocidade'];
        // $regiao->save();

        // return $regiao->recodigo;
    // }

    //retornar as regioes, coordenadas e o nome do cliente
    public function retornaRegioes()
    {

    }
    public function pontos()
    {
        return $this->hasMany('App\Models\Pontos', 'poregiao', 'recodigo');
    }

    public static function regioesCliente($cliente){
        $regioes = Regioes::where('recliente',$cliente)->get();

        return $regioes;
    }
}

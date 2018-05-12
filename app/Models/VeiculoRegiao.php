<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class VeiculoRegiao extends Model
{
    protected $primaryKey = 'vrcodigo';
    protected $table = 'veiculo_regiao';
	public $timestamps = false;
    protected $fillable = [
        "vrveiculo",
        "vrregiao"
    ];
    public function veiculo()
    {
        return $this->belongsTo('App\Models\Veiculo', 'vrveiculo', 'vecodigo');
    }
    public function regiao()
    {
        return $this->belongsTo('App\Models\Regiao', 'vrregiao', 'recodigo');
    }
}

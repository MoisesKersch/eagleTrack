<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotoristaPonto extends Model
{
	protected $table = "motorista_ponto";
	public $timestamps = false;
	protected $primaryKey = "mpcodigo";

	protected $fillable = [
			  	"mpcodigo",
			  	"mpponto",
			  	"mpmotorista"
		      ];

	public function motorista()
	{

		return $this->hasMany('App\Models\Motorista', 'mtcodigo', 'mpmotorista');
	}

	public function ponto()
	{
		return $this->hasMany('App\Models\Pontos', 'pocodigo', 'mpponto');
	}
	public function getMotoristaPonto($codEmpresas){
		$retorno = new MotoristaPonto();
		if(!empty($codEmpresas))
			$retorno = $retorno->ponto()->whereIn('pocodigocliente',$codEmpresas);
		return $retorno->get();
	}

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class Motorista extends Model
{
	protected $primaryKey = "mtcodigo";

    protected $fillable = [
      "mtnome",
      "mtcliente",
      "mtcracha",
      "created_at",
      "updated_at",
      "mtcpf",
      "mtrg",
      "mtdatanasc",
      "mtcnh",
      "mtcnhvalidade",
      "mtcnhnumero",
      "mttelefone",
      "mtstatus",
      "mtperfil",
      "mtgrupo",
      "mtendereco",
      "mtraio",
      "mtlatitude",
      "mtlongitude",
      'mtjornada'
    ];

    public function pontos() {
        return $this->belongsToMany('App\Models\Pontos', 'motorista_ponto', 'mpmotorista', 'mpponto');
    }

    public function grupo() {
        return $this->belongsTo('App\Models\GrupoMotorista', 'clcodigo', 'gmcliente');
    }

    public function grupoMotorista()
    {
      return $this->hasOne('App\Models\GrupoMotorista', 'gmcodigo', 'mtgrupo');
    }

  	public function motoristaPonto()
  	{
      return $this->belongsTo('App\Models\MotoristaPonto', 'mpmotorista', 'mtcodigo');
  	}

    public function cliente()
    {
      return $this->belongsTo('App\Models\Cliente', 'mtcliente', 'clcodigo');
    }

    public function licencas()
    {
      return $this->belongsToMany('App\Models\Licenca', 'motorista_licencas', 'mlmotorista', 'mllicenca')->withPivot('mlvalidade');
    }
	/*RETORNA TODOS MOTORISTAS, ATIVOS E INATIVOS*/
	public function getMotoristas($empresas){
		$retorno;
        if(!empty($empresas)){
            $retorno = Motorista::whereIn('mtcliente',$empresas)
								  ->get();
        }else{
            if(Auth::user()->usumaster == 'S'){
                $retorno = Motorista::where('mtstatus','=','A')->get();
            }else{
                $retorno = Motorista::where('mtstatus','=','A')
                                    ->where('mtcliente','=',Auth::user()->usucliente)
                                    ->get();
            }
        }
        return $retorno;
	}
}

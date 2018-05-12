<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DB;

class Modulo extends Model
{
    protected $primaryKey = "mocodigo";

    protected $fillable = [
       "mocodigo",
       "moimei",
       "modatainstalacao",
       "momodelo",
       "moproprietario",
       'moultimoevento',
       "mostatus",
       "mosim",
       'moultimareferencia',
       'moultimohodometro',
       'moultimohorimetro',
       'mohodometro',
       'mohorimetro'
    ];

    public function proprietario()
    {
        return $this->hasOne('App\Models\Cliente', 'clcodigo', 'moproprietario');
    }

    public function chip()
    {
    	return $this->hasOne('App\Models\Chip', 'chcodigo', 'mosim');
    }

    public function moduloModelo()
    {
    	return $this->hasOne('App\Models\ModuloModelo', 'mmcodigo', 'momodelo');
    }

    public function kmsPercorridosHoje()
    {
        $date = new DateTime();
        $date = date_format($date,"d/m/Y");

        $totalKm = Bilhete::select(DB::raw('coalesce(max(bihodometro) - min(bihodometro),0)/1000 as kms'))
            ->where('bimodulo','=',$this->mocodigo)
            ->whereBetween('bidataevento', array($date." 00:00", $this->moultimoevento))
            ->first();

        return $totalKm['kms'];
    }

}

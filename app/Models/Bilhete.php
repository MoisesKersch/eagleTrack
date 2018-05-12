<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bilhete extends Model
{
    protected $primaryKey = "bicodigo";

    protected $fillable = [
      "bicodigo",
      "bimodulo",
      "bidataprocessado",
      "bidataevento",
      "bilatlog",
      "biignicao",
      "bidirecao",
      "bivelocidade",
      "bimotivotransmissao",
      "bihodometro",
      "bialimentacao",
      "biusobateria",
      "bialimentacaodet",
      "bitemperatura",
      "bijamming",
      "bicargabateria",
      "bipanico",
      "bibloqueio",
      "bientrada01",
      "bientrada02",
      "bientrada03",
      "bientrada04",
      "bientrada05",
      "bientrada06",
      "bientrada07",
      "bientrada08",
      "bisaida01",
      "bisaida02",
      "bisaida03",
      "bisaida04",
      "bisaida05",
      "bisaida06",
      "bisaida07",
      "bisaida08",
      "bimotorista",
      "biendereco",
      "biplaca",
      "bidistultimaposicao",
      "bivelocidadetrecho",
      "bimovimento",
      "bihorimetro",
      "created_at",
      "updated_at"
    ];

}

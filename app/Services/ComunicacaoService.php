<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Modulo;
use App\Models\Cliente;
use DB;
use App\Helpers\MapaHelper;
use App\Helpers\DataHelper;
use App\Helpers\PdfHelper;
use Excel;

class ComunicacaoService
{
    public function query($tempo,$veiculos)
    {
        $comunicacoes = Modulo::select('moultimoevento', 'moultimoendereco', 'mocodigo', 'mmdescricao','veplaca', 'clfantasia')
            ->join('veiculos','vemodulo', 'mocodigo')
            ->join('clientes','clcodigo', 'veproprietario')
            ->join('modulo_modelos','mmcodigo','momodelo')
            ->where('moultimoevento','>', DB::raw("(select current_timestamp - time '".$tempo."')"))
            ->whereIn('vecodigo', $veiculos)
            ->orderBy('moultimoevento')->get();

            return $comunicacoes;
    }
}

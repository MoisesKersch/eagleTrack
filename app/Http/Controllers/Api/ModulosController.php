<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Models\Modulo;
use App\Models\UsuarioApp;
use App\Models\Bilhete;
use DB;
use App\Http\Controllers\Controller;

class ModulosController extends Controller
{
  public function getUltimaPosicaoVeiculo(Request $request)
  {
        $idmodulo = $request->input('idmodulo');

        $veiculo = DB::table('veiculos')
            ->leftJoin('modulos', 'veiculos.vemodulo', '=', 'modulos.mocodigo')
            ->where('mocodigo', '=', $idmodulo)
            ->first();

          if ($veiculo === null) {
              abort(404, 'Veículo não encontrado');
          }

          return Response::json([
              'data'=>$veiculo
          ]);
    }

    public function findByIdModulo(Request $request)
    {
        $idmodulo = $request->input('idmodulo');

        $modulo = Modulo::find($idmodulo);

        if ($modulo === null) {
           abort(404, 'Modulo não encontrado');
        }

        return Response::json([
          'data'=>$modulo
        ]);
      }
}

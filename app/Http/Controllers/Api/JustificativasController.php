<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Models\Modulo;
use App\Models\UsuarioApp;
use App\Models\Motorista;
use App\Models\Bilhete;
use App\Models\Justificativa;
use App\Models\itensRota;
use DB;
use App\Http\Controllers\Controller;

class JustificativasController extends Controller
{
  public function getAllJustificativasForUser(Request $request){
      $ids = UsuarioApp::clientesUsuarioUsuarioApp($request->input('idusuarioapp'));

      $teste = Justificativa::select('jucliente','judescricao')
      ->wherein('jucliente',$ids)->get();

      return json_encode($teste);
  }

}

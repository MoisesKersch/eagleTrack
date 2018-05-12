<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Models\UsuarioApp;
use App\Models\Veiculo;
use App\Models\Motorista;
use App\Http\Controllers\Controller;

class UsuarioAppController extends Controller
{
  public function findByUsuaCod(Request $request)
  {
      $usuarioApp = new UsuarioApp();
      $motorista = new Motorista();
      $veiculo = new Veiculo();
      $idusuarioapp = $request->input('idusuarioapp');

      $usuarioApp = UsuarioApp::with('cliente')->with('usuario')->where('usacodigo', '=', $idusuarioapp)->first();
      if ($usuarioApp === null) {
         abort(404, 'Usuário Não encontrado');
      }

      if($usuarioApp->cliente->clstatus != 'A'){
          abort(405, 'Cliente Inativo');
      }

      try {
          if($usuarioApp->usuario->usuativo != 'S'){
              abort(406, 'Usuário Inativo');
          }
      } catch (\Exception $e) {
      }

      if($usuarioApp->usaperfil == "M"){
        $motorista = Motorista::where('mtcodigo', '=', $usuarioApp->usamotorista)->first();
        if(isset($motorista)){
           $veiculo = Veiculo::where('vemotorista', '=', $motorista->mtcodigo)->first();
        }
      }

      $image = $usuarioApp->cliente->cllogo;

      return Response::json([
        'usuapp' => $usuarioApp,
        'climage' => $image,
        'motorista' => $motorista,
        'veiculo' => $veiculo
      ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use App\Models\Pontos;
use App\Models\UsuarioApp;
use App\Models\Cliente;

class PontosReferenciaController extends Controller{

  public function buscar(Request $request){
    $tokenRecebido = $request->input('token');
    $idusuarioapp = $request->input('idusuarioapp');
    $usuarioApp = UsuarioApp::find($idusuarioapp);
    $cliente = Cliente::find($usuarioApp->usacliente);

    $pontos = Pontos::where('pocodigocliente', $cliente->clcodigo)
                      ->where('potipo', 'P')
                      ->get();

    return Response::json([
      'data'=>$pontos
    ]);
  }

  public function buscarAll(Request $request)  {
    $tokenRecebido = $request->input('token');
    $idusuarioapp = $request->input('idusuarioapp');
    $usuarioApp = UsuarioApp::find($idusuarioapp);
    $cliente = Cliente::find($usuarioApp->usacliente);
    $entrega = Pontos::where('pocodigocliente', $cliente->clcodigo)
    ->where('potipo', 'E')
    ->get();

    $coleta = Pontos::where('pocodigocliente', $cliente->clcodigo)
    ->where('potipo', 'C')
    ->get();

    $referencia = Pontos::where('pocodigocliente', $cliente->clcodigo)
    ->where('potipo', 'P')
    ->get();

    return json_encode(array(
      'entrega'=>$entrega,
      'coleta'=>$coleta,
      'referencia'=>$referencia
    ));
  }
}

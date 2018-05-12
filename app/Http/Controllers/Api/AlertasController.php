<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Models\Modulo;
use DB;
use App\Models\Alerta;
use App\Models\UsuarioApp;
use App\Http\Controllers\Controller;

class AlertasController extends Controller
{

  public function getAlertas(Request $request)
  {

      $ids = UsuarioApp::clientesUsuarioUsuarioApp($request->input('idusuarioapp'));



      $alertas = Alerta::whereIn('alcliente',$ids)
                ->where('alalerta',$request->input('tipoalerta'))
                ->where('alstatus','P')->get();

      if (count($alertas) <= 0 ) {
         abort(404, 'Nenhum alerta encontrado');
      }

      return Response::json([
        'data'=>$alertas
      ]);
    }

    public function getAllAlertas(Request $request){
      //  dd($request->all());
        $ids = UsuarioApp::clientesUsuarioUsuarioApp($request->input('idusuarioapp'));
      //  dd($ids);
        $alertas = Alerta::select('alcodigo','alstatus','aldatahora','allocalizacao','alvelocidade','alalerta','veplaca')
                ->join('veiculos','vemodulo','almodulo')
                  ->whereIn('alcliente',$ids)
                  ->whereIn('alalerta',$request->input('tipoalerta'))
                  ->where(function($query){
                    $query->where('alstatus','P')
                          ->orWhere('alstatus','M');
                  })->get();

                  $alertas3 = $alertas->where('alalerta',3)->count();
                  $alertas2 = $alertas->where('alalerta',2)->count();

        return Response::json([
          'data'=>$alertas,
          'alertas2'=>$alertas2,
          'alertas3'=>$alertas3
        ]);
      }

      public function updateAlStatus(Request $request){
        $alertas = Alerta::find($request->input('alcodigo'));
        $alertas->alstatus = 'L';
        $alertas->save();
        return ['status'=>$alertas];
      }

      public function updateAlStatusPut(Request $request){
        $idsAlertas = $request->input('idsalertas');
        $alertas = Alerta::whereIn('alcodigo',$idsAlertas)
            ->update(['alstatus' => 'M']);
            return ['status'=>$alertas];
      }

    public function getCountAlertas(Request $request){
        $ids = UsuarioApp::clientesUsuarioUsuarioApp($request->input('idusuarioapp'));

        $panico = Alerta::whereIn('alcliente',$ids)->where('alalerta',2)->where('alstatus','P')->count();
        $velocidade = Alerta::whereIn('alcliente',$ids)->where('alalerta',3)->where('alstatus','P')->count();

        return Response::json([
          'panico'=>$panico,
          'velocidade'=>$velocidade
        ]);
      }
}

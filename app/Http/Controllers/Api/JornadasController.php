<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Response;
use App\Models\Modulo;
use App\Models\UsuarioApp;
use App\Models\Motorista;
use App\Models\Bilhete;
use DB;
use App\Http\Controllers\Controller;

class JornadasController extends Controller
{
    //busca dados de veículos e ajudantes
      public function getDadosJornada(Request $r){

          $modulos = Modulo::select('*')
                ->join('veiculos','vemodulo', '=', 'mocodigo')
                ->where('veproprietario', UsuarioApp::find($r->idusuarioapp)->usacliente)
                ->where('mostatus', 'A')
                ->orderBy('veplaca','ASC')
                ->get();

          $ajudantes = Motorista::select('mtcodigo','mtnome','mttelefone','mtperfil','mtcliente')
                    ->where('mtstatus','A')
                    ->where('mtcodigo', '!=',UsuarioApp::find($r->idusuarioapp)->usamotorista)
                    ->where('mtcliente', UsuarioApp::find($r->idusuarioapp)->usacliente)
                    ->where('mtperfil', 'LIKE','%A%')
                    ->get();

            return Response::json([
              'modulos'=>$modulos,
              'ajudantes'=>$ajudantes
            ]);
      }
}

<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Exception;
use App\Models\Modulo;
use App\Helpers\AuthHelper;

class CheckModuloApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $tokenRecebido = $request->input('token');
      $idmodulo = $request->input('idmodulo');
      $helper = new AuthHelper();
      $tokenGerado = $helper->gerarChave();
      $result = $helper->verificaChave($tokenGerado, $tokenRecebido);
      if((! $this->idModuloIsValid($idmodulo)) || (! $result == 0) ){
          abort(403, 'Operação não autorizada');
      }
      return $next($request);
   }

   private function idModuloisValid($idmodulo)
   {
     if(Modulo::where('mocodigo', '=', $idmodulo)->exists()){
      //  $usuarioApp = UsuarioApp::where('usacodigo', '=', $idusuarioapp)->get();
      $modulo = Modulo::find($idmodulo);
       if($modulo->mostatus != "A"){
         abort(402, 'Modulo inativo no Sistema');
       }

       return true;
     }

     return false;
   }
}

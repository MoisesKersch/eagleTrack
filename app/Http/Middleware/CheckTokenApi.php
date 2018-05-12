<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Exception;
use App\Models\UsuarioApp;
use App\Helpers\AuthHelper;

class CheckTokenApi
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
      $idusuarioapp = $request->input('idusuarioapp');
      $helper = new AuthHelper();
      $tokenGerado = $helper->gerarChave();
      $result = $helper->verificaChave($tokenGerado, $tokenRecebido);
      if((! $this->idUsuarioIsValid($idusuarioapp)) || (! $result == 0) ){
          abort(403, 'Operação não autorizada');
      }
      return $next($request);
   }

   private function idUsuarioIsValid($idusuarioapp)
   {
     if(UsuarioApp::where('usacodigo', '=', $idusuarioapp)->exists()){
      //  $usuarioApp = UsuarioApp::where('usacodigo', '=', $idusuarioapp)->get();
      $usuarioApp = UsuarioApp::find($idusuarioapp);
       if($usuarioApp->usastatus != "A"){
         abort(402, 'Usuário Inativo no Sistema');
       }

       return true;
     }

     return false;
   }
}

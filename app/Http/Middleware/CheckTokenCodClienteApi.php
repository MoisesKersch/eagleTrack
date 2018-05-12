<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Cliente;
use App\Helpers\AuthHelper;

class CheckTokenCodClienteApi
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
      $clcodigo = $request->input('codigo_cliente');
      $helper = new AuthHelper();
      $tokenGerado = $helper->gerarChave();
      $result = $helper->verificaChave($tokenGerado, $tokenRecebido);
      if((! $this->isClientValid($clcodigo)) || (! $result == 0) ){
          abort(403, 'Operação não autorizada!');
      }
      return $next($request);
   }

   private function isClientValid($clcodigo)
   {
     if(Cliente::where('clcodigo', '=', $clcodigo)->exists()){
      //  $usuarioApp = UsuarioApp::where('usacodigo', '=', $idusuarioapp)->get();
      $cliente = Cliente::find($clcodigo);
       if($cliente->clstatus != "A"){
         abort(402, 'Usuário Inativo no Sistema');
       }

       return true;
     }

     return false;
   }

}

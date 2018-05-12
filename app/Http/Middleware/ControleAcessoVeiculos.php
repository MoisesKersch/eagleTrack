<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ControleAcessoVeiculos
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

        $id = preg_replace("/[^0-9]/", "", $_SERVER["REQUEST_URI"]);

        if(strpos($_SERVER["REQUEST_URI"], 'veiculos')) {
            if($id > 0){
                $user = User::where('id', '=', \Auth::user()->id)
                    ->with('perfil.veiculos')
                    ->first();

                if($user->usumaster != 'S'){
                    $veiculos = $user->perfil->veiculos;
                    foreach ($veiculos as $veiculo) {
                        if($veiculo->vecodigo == $id) {
                            return redirect('erro');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}

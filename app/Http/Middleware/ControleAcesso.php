<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ControleAcesso
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
        if(\Auth::user()->usumaster != "S") {
            $padrao = trim(str_replace( range( 0, 9 ), null, $_SERVER["REQUEST_URI"]), '/');
            $url = explode('/', $padrao);
            $acessado = $url[count($url) - 1];
            if(count($url) > 3)
                $padrao = str_replace('/'.$acessado, '', $padrao);

            $user = User::where('id', '=', \Auth::user()->id)
                ->join('perfil_permissoes', 'ppperfilcodigo', '=', 'usuperfil')
                ->join('perfil_itens', 'ppperfilitens', '=', 'picodigo')
                ->where('piurl', 'like', '%'.$padrao.'%')
                ->first();

            if(!isset($user)) {
                return redirect('/erro');
            }elseif(strpos($_SERVER["REQUEST_URI"], 'cadastrar') && !$user->ppcadastrar) {
                return redirect('/erro');

            }elseif(strpos($_SERVER["REQUEST_URI"], 'editar') && !$user->ppeditar) {
                return redirect('/erro');

            }elseif(strpos($_SERVER["REQUEST_URI"], 'excluir') && !$user->ppexcluir) {
                return redirect('/erro');

            }elseif(strpos($_SERVER["REQUEST_URI"], 'importar') && !$user->ppimportar) {
                return redirect('/erro');

            }
        }

        return $next($request);
    }
}

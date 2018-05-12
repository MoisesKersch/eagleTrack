<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RedirectUnauthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        session(['sessao_reiniciada' => time()]);

        return $next($request);
    }
}

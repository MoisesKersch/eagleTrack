<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Hash;

class VerifyPassword
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
        if (!Hash::check($request->password, \Auth::user()->password)) {
            return response([
                'erro' => 'Senha incorreta!',
                'codigo' => '500'
            ]);
        }

        return $next($request);
    }
}

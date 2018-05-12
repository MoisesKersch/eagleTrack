<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\PerfilItens;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function username()
     {
         return 'name';
     }

    protected function authenticated(Request $request)
    {
        $permissoes = PerfilItens::select('ppvisualizar', 'piid')
                ->join('perfil_permissoes', 'ppperfilitens', '=', 'picodigo')
                ->join('perfis', 'pecodigo', 'ppperfilcodigo')
                ->join('users', 'usuperfil', '=', 'pecodigo')
                ->where('piperfilmenu', '=', 5)
                ->where('id', '=', \Auth::user()->id)
                ->get();

        if($permissoes->isEmpty()) {
            return redirect('painel');
        } else {
            return redirect('home');
        }
    }

    protected function credentials(Request $request) {
        return array_merge($request->only($this->username(), 'password'), ['usuativo' => 'S']);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}

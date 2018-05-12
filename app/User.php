<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Cliente;

class User extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "name",
        "email",
        "password",
        "remember_token",
        "created_at",
        "updated_at",
        "usucliente",
        "usumaster",
        "usuativo",
        "usuperfil"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function clientes()
    {
        // return $this->belongsToMany('Budget')->where('training_id', '=', $training_id);
        return $this->belongsToMany('App\Models\Cliente', 'usuarios_clientes', 'uclusuario', 'uclcliente')->where('clstatus', '=', 'A');
    }
    public function cliente()
    {
        return $this->hasOne('App\Models\Cliente', 'clcodigo', 'usucliente');
    }

    public function perfil()
    {
        return $this->hasOne('App\Models\Perfil', 'pecodigo', 'usuperfil');
    }
    /*
    * Retorna array com códigos das empresas associadas ao usuario
    */
    public function getEmpresasUsuario(){
        $codigosEmpresas = $this->clientes()->select('clcodigo')->get();
        $retorno = array();
        foreach($codigosEmpresas as $x){
            $retorno[] = $x->clcodigo;
        }
        return $retorno;
    }
    public function getUsuarioCliente($id)
    {
        $usuarios = User::join('clientes', 'clcodigo', '=', 'usucliente')
            ->whereIn('usucliente', $id)
            ->get();
        return $usuarios;
    }

}

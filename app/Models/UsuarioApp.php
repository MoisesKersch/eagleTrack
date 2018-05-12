<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UsuarioApp extends Model
{
    //

    protected $table = "usuario_apps";
    protected $primaryKey = "usacodigo";

    protected $fillable = [
      "usacliente",
      "usausuario",
      "usamotorista",
      "usaperfil",
      "usastatus",
      "usarastreador"
    ];

    public function motorista()
    {
        return $this->hasOne('App\Models\Motorista', 'mtcodigo', 'usamotorista');
    }

    public function cliente()
    {
        return $this->hasOne('App\Models\Cliente', 'clcodigo', 'usacliente');
    }

    public function usuario()
    {
        return $this->hasOne('App\User', 'id', 'usausuario');
    }

    // @return array clientes do usuario
    public static function clientesUsuarioUsuarioApp($idusuarioapp){

        $usuarioApp = UsuarioApp::select('usausuario')->where('usacodigo',$idusuarioapp)->first();
        $ucls = DB::table('usuarios_clientes')->select('uclcliente')->where('uclusuario',$usuarioApp->usausuario)->get()->toArray();
        $ids = [];
        foreach ($ucls as $key => $ucl) {
            array_push($ids,$ucl->uclcliente);
        }

        return $ids;

    }

}

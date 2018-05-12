<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    protected $primaryKey = "clcodigo";
    protected $fillable = [
          "clcodigo",
          "clnome",
          "clfantasia",
          "cldocumento2",
          "cllogradouro",
          "clnumero",
          "clcomplemento",
          "clbairro",
          "clcidade",
          "cllatitude",
          "cltipo",
          "cldocumento",
          "cllongitude",
          "clstatus",
          "clsegmento",
          "cllogo",
          "created_at",
          "updated_at",
          "clraio",
          "clapikey",
          "cljornadaajudante",
          "cljornadamotoristacomajudante",
          "cljornadamotoristasemajudante",
          "clmodotratamentorota"
    ];

    public function telefones()
    {
        return $this->hasMany('App\Models\Telefones', 'tlproprietario', 'clcodigo');
    }

    public function email()
    {
        return $this->hasMany('App\Models\Email', 'emproprietario', 'clcodigo');
    }
    public function cidade()
    {
        return $this->belongsTo('App\Models\Cidade', 'clcidade', 'cicodigo');
    }

    public function user()
    {
      return $this->hasMany('App\User', 'usucliente', 'clcodigo');
    }

    public function users()
    {
      return $this->hasMany('App\User', 'usucliente', 'clcodigo');
    }

    public function veiculos()
    {
        return $this->hasMany('App\Models\Veiculo', 'veproprietario', 'clcodigo');
    }

    public function modulo()
    {
      return $this->hasMany('App\Models\Modulo', 'moproprietario', 'clcodigo');
    }

    public function pontos()
    {
        return $this->hasMany('App\Models\Pontos', 'pocodigocliente', 'clcodigo');
    }
    public function motoristas()
    {
        return $this->hasMany('App\Models\Motorista', 'mtcliente', 'clcodigo');
    }
    public function ignVeiculo()
    {
        return $this->hasMany('App\Models\Motorista', 'mtcliente', 'clcodigo');
    }
    public function getClientesAtivos(){
        $retorno = Cliente::where('clstatus','=','A')->get();
        return $retorno;
    }

    public function pontosEspera()
    {
      return $this->belongsToMany('App\Models\Pontos', 'pontos_hora_espera_cliente', 'phcliente', 'phponto');
    }
    public function gruposVeiculos()
    {
      return $this->hasMany('App\Models\GrupoVeiculo', 'gvempresa', 'clcodigo');
    }

    public static function getClientesUserLogado(){

        $clientes = Cliente::select('clnome', 'clcodigo');
    		if(\Auth::user()->usumaster == 'N'){
    			$clientes->join('usuarios_clientes', 'uclcliente', '=', 'clcodigo')
    			->where('uclusuario', '=', \Auth::user()->id)
    			->where('clstatus', '=', 'A');
    		}
    	$clientes = $clientes->get();

        return $clientes;
    }

    public static function getClienteApiKey($chaveApi){
        $cliente = Cliente::select('clcodigo');
        $cliente->where('clapikey', '=', $chaveApi)
            ->where('clstatus', '=', 'A');
        $cliente = $cliente->get();
        if(count($cliente) > 0){
            return $cliente[0]->clcodigo;
        }else{
            return false;
        }
    }

    public function feriados(){
        return $this->hasMany('App\Models\Feriado', 'clcodigo', 'frcliente');
    }

    public function modulosSistema(){
        return $this->belongsToMany('App\Models\ModulosSistema', 'modulos_sistema_cliente', 'msccliente', 'mscmodulossistema');
    }

    //somente os menus que este cliente tem acesso
    public static function menusClienteSistema($id){
        $modulosSistema = (Cliente::find($id))->modulosSistema()->get();

        $ids = array(); //ids dos modulos do cliente no sistema
        foreach ($modulosSistema as $key => $ms) {
            array_push($ids,$ms->mscodigo);
        }

        // pegar os menus do cliente a partir dos módulos vinculados a ele
        $menu = PerfilMenu::with(['itens' => function($query) use ($ids){
            $query->whereIn('pimodulo_sistema', $ids);
        }])->has('itens')->get();


        // $menu = PerfilMenu::with('itens')->whereIn('pimodulo_sistema', $ids)->get();

        return $menu;
    }

}

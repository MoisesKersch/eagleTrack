<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Cliente;
use DB;
use Excel;
use App\Models\PerfilVeiculo;
use App\Models\PerfilPermissoes;

class PerfilService
{

    public static function saveRelacaoPerfilVeiculo($perfil ,$dados_all){
        foreach($dados_all['peveiculos'] as $key => $veic){
            $pv = new PerfilVeiculo();
            $pv->pvpecodigo = $perfil->pecodigo;
            $pv->pvvecodigo = $veic;
            $pv->save();
        }
    }

    public static function saveRelacaoPerfilPermissoes($perfil ,$orderMenus){
        if($orderMenus != null){
            foreach($orderMenus as $menu){
                foreach ($menu as $itens){
                    $perfilPermissoes = new PerfilPermissoes();
                    foreach ($itens as $key => $item) {
                        if($item['permissao'] == 'ppvisualizar'){
                            $perfilPermissoes->ppvisualizar = true;
                        }elseif ($item['permissao'] == 'ppcadastrar') {
                            $perfilPermissoes->ppcadastrar = true;
                        }elseif ($item['permissao'] == 'ppeditar') {
                            $perfilPermissoes->ppeditar = true;
                        }elseif ($item['permissao'] == 'ppexcluir') {
                            $perfilPermissoes->ppexcluir = true;
                        }elseif ($item['permissao'] == 'ppimportar') {
                            $perfilPermissoes->ppimportar = true;
                        }
                        $perfilPermissoes->ppperfilcodigo = $perfil->pecodigo;
                        $perfilPermissoes->ppperfilitens = $item['item'];
                    }
                    $perfilPermissoes->save();
                }
            }
        }
    }

    public static function removeOldRelacaoPerfilVeiculo($perfil){
        DB::table('perfil_veiculo')->where('pvpecodigo', '=', $perfil->pecodigo)->whereNotIn('pvvecodigo', isset(\Auth::user()->perfil) ?  \Auth::user()->perfil->veiculosNegados() : [])->delete();
    }

    public static function removeOldRelacaoPerfilPermissoes($perfil){
        DB::table('perfil_permissoes')->where('ppperfilcodigo', '=', $perfil->pecodigo)->delete();
    }

    public static function agruparMenuItem($dados){

        $menus = null;
        $old = '';
        $j = 0;
        $i = 0;

        foreach($dados as  $name => $dado) {
            $exdata = explode('-',$name);
            $perfilItem = $exdata[2];
            $perfilMenu = $exdata[1];

            if($i == 0 || $perfilMenu != $old_menu){
                $j = 0;
                $old_menu = $perfilMenu;
            }
            if($perfilMenu == $old_menu) {
                $menus[$perfilMenu][$perfilItem][$j]['permissao'] = $exdata[0];
                $menus[$perfilMenu][$perfilItem][$j]['menu'] = $exdata[1];
                $menus[$perfilMenu][$perfilItem][$j]['item'] = $exdata[2];
                $j = $j + 1;
            }else{
                $j = 0;
            }
            $old_menu = $perfilMenu;
            $i = $i + 1;
        }
        return $menus;
    }

}

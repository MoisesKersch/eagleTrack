<?php

namespace App\Helpers;
use App\Helpers\DataHelper;
use App\User;
use App\Models\PerfilPermissoes;
use App\Models\PerfilItens;

class AcessoHelper
{

	// Pegar permissoes pelo @ppperfiitensl, caso não tiver permissoes retorna @null
	public static function acessos($ppperfilitens)
	{
		$perfilIten = PerfilItens::select('picodigo')
			->where('piid',$ppperfilitens)
			->first();

		$picodigo = null;
		if(!isset($perfilIten)){
			return null;
		}else{
			$picodigo = $perfilIten->picodigo;
		}
		$permissoes = User::where('id', \Auth::user()->id)
			->with(['perfil.permissoes' => function($query) use($picodigo){
			$query->where('ppperfilitens',$picodigo);
		}])->first();

		if(count($permissoes->perfil->permissoes) > 0){
			return $permissoes->perfil->permissoes[0];
		}else{
			return null;
		}

		// $permissoes = PerfilPermissoes::where('ppperfilitens', $ppperfilitens)->where('ppperfilcodigo', \Auth::user()->perfil->pecodigo)->first();
	}

	// Pegar permissoes pelo @ppperfiitens e pela @permissao (EX: ppvisualizar), caso não tiver permissoes retorna @false
	public static function acessosPermissao($ppperfilitens, $permissao){
		$perfilIten = PerfilItens::select('picodigo')
			->where('piid',$ppperfilitens)
			->first();
			
		$picodigo = false;
		if(\Auth::user()->usumaster == 'S'){
			return true;
		}
		if(!isset($perfilIten)){
			return false;
		}else{
			$picodigo = $perfilIten->picodigo;
		}
		$permissoes = User::where('id', \Auth::user()->id)
			->with(['perfil.permissoes' => function($query) use($picodigo){
				$query->where('ppperfilitens',$picodigo);
		}])->first();

		if(count($permissoes->perfil->permissoes) > 0){
			if($permissao == 'ppvisualizar'){
				return $permissoes->perfil->permissoes[0]->ppvisualizar;
			}elseif ($permissao == 'ppcadastrar') {
				return $permissoes->perfil->permissoes[0]->ppcadastrar;
			}elseif ($permissao == 'ppeditar') {
				return $permissoes->perfil->permissoes[0]->ppeditar;
			}elseif ($permissao == 'ppexcluir') {
				return $permissoes->perfil->permissoes[0]->ppexcluir;
			}elseif ($permissao == 'ppimportar') {
				return $permissoes->perfil->permissoes[0]->ppimportar;
			}

			return false;
		}else{
			return false;
		}
	}

}

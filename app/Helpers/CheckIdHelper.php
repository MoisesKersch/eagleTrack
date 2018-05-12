<?php
namespace App\Helpers;

use DateTime;
use Hash;
use DB;

class CheckIdHelper
{
    public static function checkId($table,$columnId, $columnCli, $id)
    {

        try{
            $clientesCodigos = \Auth::user()->getEmpresasUsuario();
            if(\Auth::user()->usumaster == 'S' && count(DB::table($table)->where($columnId, $id)->get()) > 0){
                return true;
            }elseif(\Auth::user()->usumaster != 'S' && count(DB::table($table)->where($columnId, $id)->whereIn($columnCli, $clientesCodigos)->get()) > 0){
                return true;
            }
        } catch (\Exception $e) {
        
        }
        return false;
    }
}

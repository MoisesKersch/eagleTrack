<?php
namespace App\Helpers;

use Closure;
use DateTime;

class AuthHelper
{

  public function gerarChave()
  {
      $string = "eagletrack2017";
      $data = new DateTime();
      $data = $data->format('d-m-Y');

      $nova = str_replace("-", "",$data);
      $tokenGerado = md5($string.$nova);
    //  dd($tokenGerado);
      return $tokenGerado;
  }

  public function verificaChave($tokenGerado, $tokenRecebido)
  {
      $result = strcmp($tokenGerado,$tokenRecebido);

      return $result;
  }
}

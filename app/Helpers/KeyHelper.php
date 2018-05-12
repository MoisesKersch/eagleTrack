<?php
namespace App\Helpers;

use DateTime;
use Hash;
class KeyHelper
{
  public function keyGenerate()
  {
    $data = new dateTime();
    $key = Hash::make($data->format('d-m-Y H:i:s').'mira do futuro nos nozinho de com açucar'.$data->format('d-m-Y H:i:s'));
    return $key;
  }
}

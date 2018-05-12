<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class PerfilVeiculo extends Model
{
    protected $table = "perfil_veiculo";
    public $timestamps = false;
    protected $primaryKey = "pvcodigo";
    protected $fillable = [
        "pvvecodigo",
        "pvpecodigo"
    ];
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_apps', function (Blueprint $table) {
            $table->increments('usacodigo');
            $table->integer("usacliente")->nullable();
            $table->integer("usausuario")->nullable();
            $table->integer("usamotorista")->nullable();
            $table->string("usaperfil", 1)->nullable();
            $table->string("usastatus", 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario_apps');
    }
}

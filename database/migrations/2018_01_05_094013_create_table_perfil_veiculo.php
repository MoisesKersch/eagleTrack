<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePerfilVeiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfil_veiculo', function (Blueprint $table) {
            $table->increments('pvcodigo');
            $table->integer('pvvecodigo');
            $table->integer('pvpecodigo');
            $table->foreign('pvvecodigo')->references('vecodigo')->on('veiculos');
            $table->foreign('pvpecodigo')->references('pecodigo')->on('perfis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfil_veiculo');
    }
}

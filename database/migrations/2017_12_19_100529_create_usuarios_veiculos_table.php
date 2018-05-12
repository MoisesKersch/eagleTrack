<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosVeiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_veiculos', function (Blueprint $table) {
            $table->integer('uvusuario');
            $table->integer('uvveiculo');
            $table->primary(['uvusuario', 'uvveiculo']);
            $table->timestamps();
            $table->foreign('uvusuario')->references('id')->on('users');
            $table->foreign('uvveiculo')->references('vecodigo')->on('veiculos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_veiculos');
    }
}

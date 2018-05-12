<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsuarioCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_clientes', function (Blueprint $table)
    {
            $table->increments('uccodigo');
            $table->integer('uclusuario');
            $table->integer('uclcliente');
            $table->foreign('uclcliente')->references('clcodigo')->on('clientes');
            $table->foreign('uclusuario')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_clientes');
    }
}

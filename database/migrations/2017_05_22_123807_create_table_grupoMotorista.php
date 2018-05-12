<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGrupoMotorista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_motorista', function (Blueprint $table) {
            $table->increments('gmcodigo');
            $table->text('gmdescricao')->nullable();
            $table->integer('gmcliente');
            $table->foreign('gmcliente')->references('clcodigo')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_motorista');
    }
}

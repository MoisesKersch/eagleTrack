<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManutencaoProgramadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manutencao_programadas', function (Blueprint $table) {
            $table->increments('macodigo');
            $table->integer("mapcodigoveiculo")->nullable();
            $table->integer("mapkmprogramado")->nullable();
            $table->dateTime("mapdatahoralancamento")->nullable();
            $table->integer("mapusuario")->nullable();
            $table->integer("maptipomanutencao")->nullable();
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
        Schema::dropIfExists('manutencao_programadas');
    }
}

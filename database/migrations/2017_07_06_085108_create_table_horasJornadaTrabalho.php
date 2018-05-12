<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHorasJornadaTrabalho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horas_jornada_trabalho', function (Blueprint $table)
        {
            $table->increments('hjtcodigo');
            $table->time('hjtiniprimeirot');
            $table->time('hjtfimprimeirot');
            $table->time('hjtinisegundot')->nullable();
            $table->time('hjtfimsegundot')->nullable();
            $table->integer('hjtdiasemana');
            $table->integer('hjtjornada');
            $table->foreign('hjtjornada')->references('jtcodigo')->on('jornada_trabalho');
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
        Schema::dropIfExists('horas_jornada_trabalho');
    }
}

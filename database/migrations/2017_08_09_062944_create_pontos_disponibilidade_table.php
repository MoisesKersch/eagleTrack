<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePontosDisponibilidadeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pontos_disponibilidade', function (Blueprint $table) {
            $table->increments('pdicodigo');
            $table->time('pdihorainicio');
            $table->time('pdihorafim');
            $table->integer('pdidiasemana')->comment('numeros dia da semana 0=dom 1=seg 2=ter 3=qua 4=qui 5=sex 6=sab 7=seg a sex');
            $table->integer('pdicodigoponto');
            $table->timestamps();

            $table->foreign('pdicodigoponto')->references('pocodigo')->on('pontos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pontos_disponibilidade');
    }
}

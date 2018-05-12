<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeriadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feriados', function (Blueprint $table) {
            $table->increments('frcodigo');
            $table->string('frdescricao', 200);
            $table->date('frdata');
            $table->string('frtipo', 1);
            $table->integer('frcliente');
            $table->timestamps();
            $table->foreign('frcliente')->references('clcodigo')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feriados');
    }
}

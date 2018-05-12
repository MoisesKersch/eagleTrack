<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJornadasap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jornadas_app', function (Blueprint $table) {
            $table->increments('jacodigo');

            $table->integer('jatipoevento');
            $table->foreign('jatipoevento')->references('id')
              ->on('tipo_evento_app')
              ->onDelete('cascade');

            $table->integer('jacodigomotorista');
            $table->foreign('jacodigomotorista')->references('mtcodigo')
              ->on('motoristas')
              ->onDelete('cascade');

            $table->timestamp('jadataevento');
            $table->timestamp('jadataprocessamento');
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
        Schema::dropIfExists('jornadas_app');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRegioesCoordenadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('regioes_coordenadas', function (Blueprint $table)
         {
             $table->increments('rccodigo');
             $table->string('rclatitude');
             $table->string('rclongitude');
             $table->integer('rcregiao');
             $table->foreign('rcregiao')->references('recodigo')->on('regioes')
                ->onDelete('cascade');
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::dropIfExists('regioes_coordenadas');
     }
}

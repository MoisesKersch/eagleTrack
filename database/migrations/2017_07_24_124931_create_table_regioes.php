<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRegioes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('regioes', function (Blueprint $table)
         {
             $table->increments('recodigo');
             $table->text('redescricao');
             $table->decimal('revelocidade', 10,2)->nullable();
             $table->string('recor');
             $table->integer('recliente');
             $table->foreign('recliente')->references('clcodigo')->on('clientes');
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
         Schema::dropIfExists('regioes');
     }
}

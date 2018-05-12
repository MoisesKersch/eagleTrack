<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIgnicaoVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ignicao_veiculos', function (Blueprint $table)
         {
             $table->increments('ivcodigo');
             $table->dateTime('ivdataevento');
             $table->text('ivplaca');
             $table->integer('ivmotorista');
             $table->integer('ivmotivotransmissao');
             $table->integer('ivcliente');
             $table->integer('ivponto');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ignicao_veiculos');
    }
}

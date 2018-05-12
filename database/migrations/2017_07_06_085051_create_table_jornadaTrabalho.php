<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJornadaTrabalho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jornada_trabalho', function (Blueprint $table)
        {
            $table->increments('jtcodigo');
            $table->text('jtdescricao')->nullabel();
            $table->integer('jtcliente');
            $table->string('jtstatus');
            $table->foreign('jtcliente')->references('clcodigo')->on('clientes');
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
        Schema::dropIfExists('jornada_trabalho');
    }
}

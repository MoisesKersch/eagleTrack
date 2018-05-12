<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComandosRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comandos_respostas', function (Blueprint $table) {
            $table->increments('crcodigo');
            $table->text('crcomando');
            $table->integer('crmodulo');
            $table->timestamp('crdatahora');
            $table->integer('crcodigocomando');
            $table->string('crstatus', 1);
            $table->timestamps();

            $table->foreign('crcodigocomando')->references('cfcodigo')->on('comandos_fila')->onDelete('cascade');
            $table->foreign('crmodulo')->references('mocodigo')->on('modulos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comandos_respostas');
    }
}

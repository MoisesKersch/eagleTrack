<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComandosFilasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comandos_fila', function (Blueprint $table) {
            $table->increments('cfcodigo');
            $table->integer('cfmodelorastreador');
            $table->text('cfparametro');
            $table->integer('cfmodulo');
            $table->text('cfcomando');
            $table->string('cfstatus', 1);
            $table->timestamps();

            $table->foreign('cfmodelorastreador')->references('mmcodigo')->on('modulo_modelos')->onDelete('cascade');
            $table->foreign('cfmodulo')->references('mocodigo')->on('modulos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comandos_filas');
    }
}

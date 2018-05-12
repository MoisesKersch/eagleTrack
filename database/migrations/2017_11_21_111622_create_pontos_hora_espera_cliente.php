<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePontosHoraEsperaCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pontos_hora_espera_cliente', function (Blueprint $table) {
            $table->timestamps();
            $table->integer('phponto');
            $table->integer('phcliente');

            $table->primary(['phponto', 'phcliente']);

            $table->foreign('phponto')->references('pocodigo')->on('pontos');
            $table->foreign('phcliente')->references('clcodigo')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pontos_hora_espera_cliente');
    }
}

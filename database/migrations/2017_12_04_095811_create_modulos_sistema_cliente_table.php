<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulosSistemaClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos_sistema_cliente', function (Blueprint $table) {
            $table->integer('msccliente');
            $table->integer('mscmodulossistema');
            $table->timestamps();

            $table->primary(['msccliente', 'mscmodulossistema']);

            $table->foreign('msccliente')->references('clcodigo')->on('clientes');
            $table->foreign('mscmodulossistema')->references('mscodigo')->on('modulos_sistema');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulos_sistema_cliente');
    }
}

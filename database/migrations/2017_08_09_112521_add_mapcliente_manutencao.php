<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMapclienteManutencao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manutencao_programadas', function (Blueprint $table) {
            $table->integer('mapcliente');
            $table->foreign('mapcliente')->references('clcodigo')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manutencao_programadas', function (Blueprint $table) {
            $table->dropColumn('mapcliente');
        });
    }
}

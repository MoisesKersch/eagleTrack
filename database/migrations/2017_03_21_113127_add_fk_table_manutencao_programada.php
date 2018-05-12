<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkTableManutencaoProgramada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manutencao_programadas', function (Blueprint $table) {
            $table->foreign('maptipomanutencao')->references('ticodigo')->on('tipo_manutencoes')->onDelete('cascade');
            $table->foreign('mapusuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('maptipomanutencao');
        $table->dropColumn('mapusuario');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentsForRotas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotas', function (Blueprint $table) {
            // $table->string('rostatus')->comment('P - Rota pendente, ou seja, veículo ainda não iniciou o trajeto
            //     C - Veículo está em ponto de partida, em provável carregamento
            //     I - Rota iniciada
            //     F - Rota finalizada
            //     V - Criado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('rotas');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToRelationTableVeiculoRegiao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('veiculo_regiao', function (Blueprint $table) {
            $table->foreign('vrveiculo')->references('vecodigo')->on('veiculos')->onDelete('cascade');
            $table->foreign('vrregiao')->references('recodigo')->on('regioes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('veiculo_regiao', function (Blueprint $table) {
            //
        });
    }
}

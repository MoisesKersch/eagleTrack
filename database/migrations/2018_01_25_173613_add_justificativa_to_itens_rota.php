<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJustificativaToItensRota extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->integer('irjustificativa')->nullable();
            $table->foreign('irjustificativa')->references('jucodigo')->on('justificativas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->dropForeign(['irjustificativa']);
            $table->dropColumn('irjustificativa');
        });
    }
}

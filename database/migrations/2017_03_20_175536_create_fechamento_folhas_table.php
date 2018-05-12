<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFechamentoFolhasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fechamento_folhas', function (Blueprint $table) {
            $table->increments('fecodigo');
            $table->time('fehoratrabalhada')->nullable();
            $table->time('fehorafalta')->nullable();
            $table->time('fehoraextra')->nullable();
            $table->time('fehoracemcento')->nullable();
            $table->time('fehoranoturna')->nullable();
            $table->time('feextranoturno')->nullable();
            $table->time('fehoraespera')->nullable();
            $table->time('fehorarefeicao')->nullable();
            $table->dateTime('fedataentrada')->nullable();
            $table->time('feintervalo')->nullable();
            $table->dateTime('fevoltatrabalhar')->nullable();
            $table->dateTime('fimexpediente')->nullable();
            $table->integer('fenotorista')->nullable();
            $table->dateTime('fefimexpediente')->nullable();
            $table->integer('femotorista')->nullable();
            $table->integer('fedsr')->nullable();
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
        Schema::dropIfExists('fechamento_folhas');
    }
}

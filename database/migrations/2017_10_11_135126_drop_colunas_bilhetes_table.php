<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColunasBilhetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bilhetes', function (Blueprint $table){
            $table->dropColumn(['bidistultimaposicao','bivelocidadetrecho']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bilhetes', function (Blueprint $table){
            $table->float('bidistultimaposicao',8,2)->nullable();
            $table->float('bivelocidadetrecho',4,2)->nullable();
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHistoricoRegiao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico_regiao', function (Blueprint $table) {
            $table->increments('hrcodigo');
            $table->integer('hrregiao')->nullable();
            $table->integer('hrbilhete')->nullable();
            $table->integer('hrplaca')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

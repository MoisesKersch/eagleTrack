<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePontosLinhas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pontos_linhas', function (Blueprint $table) {
            $table->increments('plcodigo');
            $table->integer('pllicodigo');
            $table->integer('plpocodigo');
            $table->integer('plpoordem');
            $table->timestamp('pltempoestimado')->nullable();
            $table->timestamps();

            $table->foreign('pllicodigo')->references('licodigo')->on('linhas');
            $table->foreign('plpocodigo')->references('pocodigo')->on('pontos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pontos_linhas');
    }
}

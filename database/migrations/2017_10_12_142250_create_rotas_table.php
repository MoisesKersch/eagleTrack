<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rotas', function (Blueprint $table) {
            $table->increments('rocodigo');
            $table->date('rodata');
            $table->integer('ropontosaida');
            $table->dateTime('rodatahorainicio')->nullable();
            $table->integer('ropontoretorno');
            $table->dateTime('rodatahorafim')->nullable();
            $table->string('rostatus');
            $table->string('roplaca');
            $table->string('rohodometroinicio')->nullable();
            $table->float('rocubagem');
            $table->integer('roqtde');
            $table->integer('rocliente');
            $table->float('ropeso');
            $table->float('rovalor');
            $table->string('rocor')->nullable();
            $table->time('rotempo')->nullable();
            $table->float('rokm')->nullable();

            $table->timestamps();

            $table->foreign('ropontosaida')->references('pocodigo')->on('pontos');
            $table->foreign('ropontoretorno')->references('pocodigo')->on('pontos');
            $table->foreign('rocliente')->references('clcodigo')->on('clientes');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rotas');
    }
}

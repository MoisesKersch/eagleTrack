<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VeiculoPerfilInstalacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veiculo_perfil_instalacao', function (Blueprint $table) {
            $table->increments('vpicodigo');
            $table->integer('vpibloqueio')->nullable();
            $table->integer('vpipanico')->nullable();
            $table->integer('vpibau')->nullable();
            $table->integer('vpibetoneira')->nullable();
            $table->integer('vpiporta')->nullable();
            $table->integer('vpitemperatura')->nullable();
            $table->integer('vpisirene')->nullable();
            $table->integer('vpiveiculo')->nullable();
            $table->foreign('vpiveiculo')->references('vecodigo')->on('veiculos');
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
        Schema::dropIfExists('veiculo_perfil_instalacao');
    }
}

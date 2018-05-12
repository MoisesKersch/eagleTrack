<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfilPermissoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfil_permissoes', function (Blueprint $table) {
            $table->increments('ppcodigo');
            $table->boolean('ppvisualizar')->nullable();
            $table->boolean('ppcadastrar')->nullable();
            $table->boolean('ppeditar')->nullable();
            $table->boolean('ppexcluir')->nullable();
            $table->boolean('ppimportar')->nullable();
            $table->integer('ppperfilitens');
            $table->timestamps();

            $table->foreign('ppperfilitens')->references('picodigo')->on('perfil_itens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfil_permissoes');
    }
}

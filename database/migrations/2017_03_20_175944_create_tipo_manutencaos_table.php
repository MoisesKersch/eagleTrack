<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoManutencaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_manutencoes', function (Blueprint $table) {
            $table->increments('ticodigo');
            $table->text("timdescricao")->nullable();
            $table->integer("timkmpadrao")->default('0');
            $table->integer("timproprietario")->nullable();
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
        Schema::dropIfExists('tipo_manutencaos');
    }
}

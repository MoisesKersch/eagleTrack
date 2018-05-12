<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVeiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->increments('vecodigo');
            $table->string("veprefixo", 20)->nullable();
            $table->string("veplaca", 10)->nullable();
            $table->string("vechassi", 50)->nullable();
            $table->double("vemaxpeso")->nullable();
            $table->double("vecubagem")->nullable();
            $table->double("veautonomia")->nullable();
            $table->integer("vemaxentregas")->nullable();
            $table->time("vemaxhoras")->nullable();
            $table->double("vecusto")->nullable();
            $table->integer("veproprietario")->nullable();
            $table->integer("vemodulo")->nullable();
            $table->integer("vemotorista")->nullable();
            $table->decimal("vevelocidademax", 10,2)->default('80');
            $table->text("vedescricao")->nullable();
            $table->string("vestatus", 1)->nullable();
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
        Schema::dropIfExists('veiculos');
    }
}

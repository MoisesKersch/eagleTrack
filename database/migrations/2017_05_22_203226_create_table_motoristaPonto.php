<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMotoristaPonto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista_ponto', function (Blueprint $table) {
            $table->increments('mpcodigo');
            $table->integer('mpponto');
            $table->foreign('mpponto')->references('pocodigo')->on('pontos');
            $table->integer('mpmotorista');
            $table->foreign('mpmotorista')->references('mtcodigo')->on('motoristas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('motorista_ponto', function (Blueprint $table) {
            $table->dropColumn('mpcodigo');
            $table->dropColumn('mpponto');
            $table->dropColumn('mpmotorista');
        });
    }
}

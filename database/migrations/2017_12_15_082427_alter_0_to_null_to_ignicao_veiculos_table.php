<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter0ToNullToIgnicaoVeiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ignicao_veiculos', function (Blueprint $table) {
            $table->integer('ivajudante')->nullable()->change();
            $table->integer('ivcliente')->nullable()->change();
            $table->integer('ivponto')->nullable()->change();
            $table->integer('ivmotorista')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ignicao_veiculos', function (Blueprint $table) {
            //
        });
    }
}

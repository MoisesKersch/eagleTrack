<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdxIvclienteToIgnicaoVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ignicao_veiculos', function (Blueprint $table) {
            $table->index('ivcliente');
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
            $table->dropIndex('ivcliente');
        });
    }
}

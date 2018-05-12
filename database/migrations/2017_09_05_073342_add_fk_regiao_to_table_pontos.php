<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkRegiaoToTablePontos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pontos', function (Blueprint $table) {
            $table->integer('poregiao')->nullable();
            $table->foreign('poregiao')->references('recodigo')->on('regioes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pontos', function (Blueprint $table) {
            //
        });
    }
}

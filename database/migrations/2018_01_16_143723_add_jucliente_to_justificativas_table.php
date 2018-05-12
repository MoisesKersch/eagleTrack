<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJuclienteToJustificativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('justificativas', function (Blueprint $table) {
            $table->integer('jucliente')->nullable();
            $table->foreign('jucliente')->references('clcodigo')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('justificativas', function (Blueprint $table) {
            $table->dropColumn('jucliente');
        });
    }
}

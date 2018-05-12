<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkTableModulos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->foreign('momodelo')->references('mmcodigo')->on('modulo_modelos')->onDelete('cascade');
            $table->foreign('mosim')->references('chcodigo')->on('chips')->onDelete('cascade');
            $table->foreign('moproprietario')->references('clcodigo')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('momodelo');
        $table->dropColumn('mosim');
        $table->dropColumn('moproprietario');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkTableAlerta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alertas', function (Blueprint $table) {
              $table->foreign('alcliente')->references('clcodigo')->on('clientes')->onDelete('cascade');
        });
        Schema::table('alertas', function (Blueprint $table) {
              $table->foreign('almodulo')->references('mocodigo')->on('modulos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn('alcliente');
        });
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn('almodulo');
        });
    }
}

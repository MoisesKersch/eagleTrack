<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetStatusDefaultVeiculosClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string("clstatus", 1)->default('A')->nullable()->change();
        });
        Schema::table('veiculos', function (Blueprint $table) {
            $table->string("vestatus", 1)->default('A')->nullable()->change();
        });
    }
}

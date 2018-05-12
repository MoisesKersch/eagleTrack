<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTableVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('veiculos', function($table) {
            $table->string('veestradaterra',1)->default('N');
            $table->string('vebalsas',1)->default('N');
            $table->string('vepedagios',1)->default('S');
            $table->string('veroterizar',1)->default('S');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('veiculos', function($table) {
            $table->dropColumn('veestradaterra');
            $table->dropColumn('vebalsas');
            $table->dropColumn('vepedagios');
            $table->dropColumn('veroterizar');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJornadaFixaToJornadaTrabalhoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jornada_trabalho', function (Blueprint $table) {
            $table->string('jttipo', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jornada_trabalho', function (Blueprint $table) {
            $table->dropColumn('jttipo');
        });
    }
}

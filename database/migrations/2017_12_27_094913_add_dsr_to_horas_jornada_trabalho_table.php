<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDsrToHorasJornadaTrabalhoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('horas_jornada_trabalho', function (Blueprint $table) {
            $table->integer('hjtdsr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('horas_jornada_trabalho', function (Blueprint $table) {
            $table->dropColumn('hjtdsr');
        });
    }
}

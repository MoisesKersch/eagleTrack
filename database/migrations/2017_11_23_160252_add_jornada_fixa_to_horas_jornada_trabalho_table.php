<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJornadaFixaToHorasJornadaTrabalhoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('horas_jornada_trabalho', function (Blueprint $table) {
            $table->time('hjttotalhoras')->nullable();
            $table->time('hjtintervalo')->nullable();
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
            $table->dropColumn('hjttotalhoras', 'hjtintervalo');
        });
    }
}

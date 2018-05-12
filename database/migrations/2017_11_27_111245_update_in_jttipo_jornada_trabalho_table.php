<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInJttipoJornadaTrabalhoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("UPDATE jornada_trabalho
        SET jttipo='F'
        WHERE jttipo is null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jornada_trabalho', function (Blueprint $table) {
            //
        });
    }
}

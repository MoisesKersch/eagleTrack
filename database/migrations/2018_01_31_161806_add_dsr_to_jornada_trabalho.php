<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDsrToJornadaTrabalho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jornada_trabalho', function (Blueprint $table) {
            $table->integer('jtdsr')->nullable()->comment('Descanso semanal remunerado');
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
            $table->dropColumn('jtdsr');
        });
    }
}

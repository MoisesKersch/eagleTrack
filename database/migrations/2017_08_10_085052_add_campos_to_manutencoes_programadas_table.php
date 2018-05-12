<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposToManutencoesProgramadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manutencao_programadas', function (Blueprint $table) {
            $table->string('mapstatus',10)->nullable();
            $table->integer('mapkmrealizado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manutencao_programadas', function (Blueprint $table) {
          $table->dropColumn('mapstatus');
          $table->dropColumn('mapkmrealizado');
        });
    }
}

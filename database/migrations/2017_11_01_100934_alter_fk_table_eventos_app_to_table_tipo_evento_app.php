<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFkTableEventosAppToTableTipoEventoApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eventos_app', function (Blueprint $table) {
            $table->dropForeign('eventos_app_eatipoevento_foreign');
            $table->foreign('eatipoevento')->references('tecodigo')->on('tipo_evento_app');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eventos_app', function (Blueprint $table) {
            //
        });
    }
}

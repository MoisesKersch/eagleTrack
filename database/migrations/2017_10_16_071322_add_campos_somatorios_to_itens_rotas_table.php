<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposSomatoriosToItensRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->float('irtempoprevisto')->nullable();
            $table->float('irdistancia')->nullable();
            $table->foreign('irrota')->references('rocodigo')->on('rotas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->dropColumn('irtempoprevisto');
            $table->dropColumn('irdistancia');
        });
    }
}

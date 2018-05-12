<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunTempoParadoToTableItensRotas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->bigInteger('irtempoparado')->nullable();
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
            $table->dropColumn('irtempoparado');
        });
    }
}

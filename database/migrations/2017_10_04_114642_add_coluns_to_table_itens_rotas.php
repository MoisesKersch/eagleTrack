<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunsToTableItensRotas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->timestamp('irdata_hora_evento')->nullable();
            $table->bigInteger('irhodometro')->nullable();
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
            $table->dropColumn('irdata_hora_evento');
            $table->dropColumn('irhodometro');
        });
    }
}

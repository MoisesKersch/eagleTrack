<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFksTableHistoricoRegiao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico_regiao', function (Blueprint $table) {
            $table->foreign('hrregiao')->references('recodigo')->on('regioes')->onDelete('set null');
            $table->foreign('hrbilhete')->references('bicodigo')->on('bilhetes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

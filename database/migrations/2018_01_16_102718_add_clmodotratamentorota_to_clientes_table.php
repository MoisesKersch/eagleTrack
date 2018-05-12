<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClmodotratamentorotaToClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('clmodotratamentorota', 1)->nullable()->comment = "Informações usadas no aplicativo A=atraves do aplicativo- I=Baseaddo na ignicao e posicionamento do veiculo";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('clmodotratamentorota');
        });
    }
}

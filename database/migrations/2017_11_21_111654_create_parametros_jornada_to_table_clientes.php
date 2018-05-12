<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametrosJornadaToTableClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table){
            $table->string('cljornadamotoristacomajudante', 1)->nullable();
            $table->string('cljornadamotoristasemajudante', 1)->nullable();
            $table->string('cljornadaajudante', 1)->nullable();
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
            $table->dropColumn('cljornadamotoristacomajudante',
                'cljornadamotoristasemajudante',
                'cljornadaajudante');
        });
    }
}

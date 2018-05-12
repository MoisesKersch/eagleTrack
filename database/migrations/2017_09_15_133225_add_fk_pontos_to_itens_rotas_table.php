<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkPontosToItensRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_rotas', function (Blueprint $table) {
            $table->integer('irponto')->nullable();
            $table->foreign('irponto')->references('pocodigo')->on('pontos')->onDelete('cascade');
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
            //
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnModulos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modulos', function (Blueprint $table){
            $table->integer('moultimoponto')->default(0);
            $table->integer('moultimaregiao')->default(0);
            $table->text('moultimareferencia')->default('Nenhuma');
            $table->integer('moultimarota')->default(0);
            $table->integer('moultimomotorista')->default(0);
            $table->text('moultimoendereco')->default('Nenhum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modulos', function (Blueprint $table){
            $table->dropColumn('moultimoponto','moultimaregiao','moultimareferencia',
                               'moultimarota','moultimomotorista','moultimoendereco');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunasBilhetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bilhetes', function (Blueprint $table){
            $table->integer('biantifurto')->default(0);
            $table->integer('biregiao')->default(0);
            $table->integer('biponto')->default(0);
            $table->text('bireferencia')->default('Nada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bilhetes', function (Blueprint $table){
            $table->dropColumn(['biantifurto','biregiao','biponto','bireferencia']);
        });
    }
}

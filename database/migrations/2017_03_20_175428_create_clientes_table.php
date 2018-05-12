<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('clcodigo');
            $table->string("clnome", 50)->nullable();
            $table->string("clfantasia", 50)->nullable();
            $table->string("cldocumento2", 50)->nullable();
            $table->string("cllogradouro", 200)->nullable();
            $table->string("clnumero", 7)->nullable();
            $table->string("clcomplemento", 100)->nullable();
            $table->string("clbairro", 50)->nullable();
            $table->string("clfone", 20)->nullable();
            $table->string("clemail", 50)->nullable();
            $table->integer("clcidade");
            $table->string("cllatlog", 50)->nullable();
            $table->string("cltipo", 1)->nullable();
            $table->string("cldocumento", 18)->nullable();
            $table->string("cllocalizacao", 50)->nullable();
            $table->string("clstatus", 1)->nullable();
            $table->string("clsegmento", 1)->nullable();
            $table->text("cllogo")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}

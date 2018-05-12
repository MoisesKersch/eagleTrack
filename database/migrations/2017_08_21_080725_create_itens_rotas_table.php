<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItensRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itens_rotas', function (Blueprint $table) {
            $table->increments('ircodigo');
            $table->integer('ircliente');
            $table->text('ircodigoexterno')->nullable();
            $table->text('irdocumento')->nullable();
            $table->text('irnome')->nullable();
            $table->date('irdata')->nullable();
            $table->integer('irqtde')->nullable();
            $table->float('ircubagem', 10,6)->nullable();
            $table->float('irpeso', 10,3)->nullable();
            $table->float('irvalor', 10,2)->nullable();
            $table->string('irplaca', 10)->nullable();
            $table->string('irstatus', 1)->nullable();
            $table->integer('irrota')->nullable();
            $table->timestamps();

            $table->foreign('ircliente')->references('clcodigo')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itens_rotas');
    }
}

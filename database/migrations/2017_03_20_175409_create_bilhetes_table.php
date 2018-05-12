<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBilhetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bilhetes', function (Blueprint $table) {
            $table->bigIncrements('bicodigo');
            $table->integer('bimodulo')->nullable();
            $table->dateTime('bidataprocessado')->nullable();
            $table->dateTime('bidataevento')->nullable();
            $table->text('bilatlog')->nullable();
            $table->integer('biignicao')->nullable();
            $table->integer('bidirecao')->nullable();
            $table->integer('bivelocidade')->nullable();
            $table->integer('bimotivotransmissao')->nullable();
            $table->integer('bihodometro')->nullable();
            $table->decimal('bialimentacao')->nullable();
            $table->decimal('biusobateria')->nullable();
            $table->decimal('bialimentacaodet')->nullable();
            $table->decimal('bitemperatura')->nullable();
            $table->integer('bijamming')->nullable();
            $table->decimal('bicargabateria')->nullable();
            $table->integer('bipanico')->nullable();
            $table->integer('bibloqueio')->nullable();
            $table->integer('bientrada01')->nullable();
            $table->integer('bientrada02')->nullable();
            $table->integer('bientrada03')->nullable();
            $table->integer('bientrada04')->nullable();
            $table->integer('bientrada05')->nullable();
            $table->integer('bientrada06')->nullable();
            $table->integer('bientrada07')->nullable();
            $table->integer('bientrada08')->nullable();
            $table->integer('bisaida01')->nullable();
            $table->integer('bisaida02')->nullable();
            $table->integer('bisaida03')->nullable();
            $table->integer('bisaida04')->nullable();
            $table->integer('bisaida05')->nullable();
            $table->integer('bisaida06')->nullable();
            $table->integer('bisaida07')->nullable();
            $table->integer('bisaida08')->nullable();
            $table->integer('bimotorista')->nullable();
            $table->text('biendereco')->nullable();
            $table->text('biplaca')->nullable();
            $table->decimal('bidistultimaposicao')->nullable();
            $table->decimal('bivelocidadetrecho')->nullable();
            $table->integer('bimovimento')->nullable();
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
        Schema::dropIfExists('bilhetes');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTipoAlerta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('tipo_alerta', function (Blueprint $table) {
          $table->increments('tacodigo');
          $table->string('tadescricao')->nullable();
          $table->string('tasirene',1)->nullable()->default('N');
          $table->string('tabloqueio',1)->nullable()->default('N');
          $table->string('taentrada1',1)->nullable()->default('N');
          $table->string('taentrada2',1)->nullable()->default('N');
          $table->string('taentrada3',1)->nullable()->default('N');
          $table->string('taentrada4',1)->nullable()->default('N');
          $table->string('tasaida1',1)->nullable()->default('N');
          $table->string('tasaida2',1)->nullable()->default('N');
          $table->string('tasaida3',1)->nullable()->default('N');
          $table->string('tasaida4',1)->nullable()->default('N');
          $table->string('taicone',100)->nullable();
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
          Schema::dropIfExists('tipo_alerta');
    }
}

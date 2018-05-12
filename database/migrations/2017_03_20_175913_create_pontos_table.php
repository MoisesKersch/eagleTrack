<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePontosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pontos', function (Blueprint $table) {
            $table->increments('pocodigo');
            $table->text("pocodigoexterno")->nullable();
            $table->text("podescricao")->nullable();
            $table->integer("pocodigocliente")->nullable();
            $table->string("potipo", 1)->nullable();
            $table->double("polatitude")->nullable();
            $table->double("polongitude")->nullable();
            $table->text("poendereco")->nullable();
            $table->integer("poraio")->nullable();
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
        Schema::dropIfExists('pontos');
    }
}

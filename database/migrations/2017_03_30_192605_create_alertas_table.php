<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->increments('alcodigo');
            $table->integer("alcliente")->nullable();
            $table->integer("almodulo")->nullable();
            $table->string("alinfoalerta")->nullable();
            $table->string("alstatus")->nullable();
            $table->timestamp("aldatahora");
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
        Schema::dropIfExists('alertas');
    }
}

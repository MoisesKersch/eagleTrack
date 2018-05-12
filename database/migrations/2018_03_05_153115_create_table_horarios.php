<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHorarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->increments('hrcodigo');
            $table->timestamp('hrhorario')->nullable();
            $table->integer('hrdiasemana');
            $table->integer('hrlicodigo');
            $table->timestamps();

            $table->foreign('hrlicodigo')->references('licodigo')->on('linhas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios');
    }
}

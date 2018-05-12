<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristaLiceencasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista_licencas', function (Blueprint $table) {
            $table->date('mlvalidade');
            $table->integer('mlmotorista');
            $table->integer('mllicenca');

            $table->primary(['mllicenca', 'mlmotorista']);
            
            $table->foreign('mllicenca')->references('licodigo')->on('licencas');
            $table->foreign('mlmotorista')->references('mtcodigo')->on('motoristas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motorista_licencas');
    }
}

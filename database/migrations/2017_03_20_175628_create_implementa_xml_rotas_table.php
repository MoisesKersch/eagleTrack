<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImplementaXmlRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('implementa_xml_rotas', function (Blueprint $table) {
            $table->increments('ipcodigo');
            $table->dateTime("ipdatahorainicio")->nullable();
            $table->dateTime("ipdatahorafinal")->nullable();
            $table->string("ipcpfcnpjpartida")->nullable();
            $table->string("ipcpfcnpjchegada", 30)->nullable();
            $table->string("ipplaca", 30)->nullable();
            $table->text("ipcpfcnpjcliente")->nullable();
            $table->text("ipcodigorota")->nullable();
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
        Schema::dropIfExists('implementa_xml_rotas');
    }
}

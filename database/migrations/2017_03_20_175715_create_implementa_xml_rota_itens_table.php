<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImplementaXmlRotaItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('implementa_xml_rota_itens', function (Blueprint $table) {
            $table->increments('iicodigo');
            $table->decimal("iiquantidade", 10,2)->nullable();
            $table->decimal("iipeso", 10,2)->nullable();
            $table->decimal("iicubagem", 10,2)->nullable();
            $table->decimal("iivalor", 10,2)->nullable();
            $table->integer("iinota")->nullable();
            $table->integer("iipontoentrega")->nullable();
            $table->text("iicodigorota")->nullable();
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
        Schema::dropIfExists('implementa_xml_rota_itens');
    }
}

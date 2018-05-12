<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfilItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfil_itens', function (Blueprint $table) {
            $table->increments('picodigo');
            $table->text('pidescricao');
            $table->integer('piperfilmenu');
            $table->timestamps();

            $table->foreign('piperfilmenu')->references('pmcodigo')->on('perfil_menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfil_itens');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkTableUsuarioApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuario_apps', function (Blueprint $table) {
            $table->foreign('usacliente')->references('clcodigo')->on('clientes')->onDelete('cascade');
            $table->foreign('usausuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('usamotorista')->references('mtcodigo')->on('motoristas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('usacliente');
        $table->dropColumn('usausuario');
        $table->dropColumn('usamotorista');
    }
}

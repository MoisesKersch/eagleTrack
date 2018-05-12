<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultValuesTablePerfilPermissoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perfil_permissoes', function (Blueprint $table) {
            $table->boolean('ppvisualizar')->default(false)->change();
            $table->boolean('ppcadastrar')->default(false)->change();
            $table->boolean('ppeditar')->default(false)->change();
            $table->boolean('ppexcluir')->default(false)->change();
            $table->boolean('ppimportar')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perfil_permissoes', function (Blueprint $table) {
            //
        });
    }
}

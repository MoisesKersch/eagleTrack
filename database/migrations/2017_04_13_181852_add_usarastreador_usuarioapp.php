<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsarastreadorUsuarioapp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('usuario_apps', function (Blueprint $table) {
        $table->string("usarastreador",1)->default('N');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuario_apps', function (Blueprint $table) {
          $table->dropColumn('usarastreador');
        });
    }
}

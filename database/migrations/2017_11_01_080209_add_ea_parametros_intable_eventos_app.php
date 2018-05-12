<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEaParametrosIntableEventosApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eventos_app', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos_app', 'eaparametro'))
              $table->string("eaparametro")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eventos_app', function (Blueprint $table) {
            $table->dropColumn("eaparametro");
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFehorastotalFechamentoFolhasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fechamento_folhas', function (Blueprint $table) {
            $table->time('fehorastotal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fechamento_folhas', function (Blueprint $table) {
            $table->dropColumn('fehorastotal');
        });
    }
}

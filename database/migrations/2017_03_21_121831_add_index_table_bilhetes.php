<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexTableBilhetes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bilhetes', function (Blueprint $table) {
            $table->index('bimodulo');
            $table->index('bidataevento');
            $table->index('bilatlog');
            $table->index('biplaca');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('bimodulo');
        $table->dropColumn('bidataevento');
        $table->dropColumn('bilatlog');
        $table->dropColumn('biplaca');
    }
}

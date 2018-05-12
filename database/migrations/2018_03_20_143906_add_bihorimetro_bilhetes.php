<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBihorimetroBilhetes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bilhetes', function (Blueprint $table) {
            $table->bigInteger('bihorimetro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bilhetes', function (Blueprint $table) {
            $table->dropColumn('bihorimetro');
        });
    }
}

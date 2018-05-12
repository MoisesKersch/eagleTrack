<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDefaultVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('veiculos', function ($table) {
            $table->float('veautonomia')->default(0)->change();
            $table->float('vecusto')->default(0)->change();
            $table->text('vemaxhoras')->default('00:00')->change();
            $table->float('vemaxpeso')->default(0)->change();
            $table->float('vecubagem')->default(0)->change();
            $table->integer('vemaxentregas')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

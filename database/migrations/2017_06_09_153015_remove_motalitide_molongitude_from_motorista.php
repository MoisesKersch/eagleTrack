<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMotalitideMolongitudeFromMotorista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('motoristas', function (Blueprint $table) {
        if (Schema::hasColumn('motoristas', 'molatitude')){
            $table->dropColumn('molatitude');
        }
        if (Schema::hasColumn('motoristas', 'molongitude')){
            $table->dropColumn('molongitude');
        }
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

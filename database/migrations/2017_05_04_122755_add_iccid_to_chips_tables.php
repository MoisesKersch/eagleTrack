<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIccidToChipsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      if(!Schema::hasColumn('chips', 'iccid')) {
          Schema::table('chips', function (Blueprint $table) {
            $table->string('iccid')->nullable();
          });
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('chips', function (Blueprint $table) {
          $table->dropColumn('iccid');
      });
    }
}

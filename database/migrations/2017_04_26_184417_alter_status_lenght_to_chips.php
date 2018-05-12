<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusLenghtToChips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('chips', function (Blueprint $table) {
          $table->dropColumn('chstatus');
      });
      Schema::table('chips', function (Blueprint $table) {
            $table->string('chstatus', 1)->default('I');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('chips', function (Blueprint $table) {
          $table->dropColumn('chstatus');
      });
    }
}

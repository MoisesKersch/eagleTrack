<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValueTousumasterUsers extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
      Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('usumaster');
      });

      Schema::table('users', function (Blueprint $table) {
            $table->string('usumaster', 1)->default('N');
      });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('usumaster');
      });
  }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUtimomotivotransmissaoModulo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('modulos', 'moultimomotivotransmissao')) {
            Schema::table('modulos', function (Blueprint $table) {
              $table->integer('moultimomotivotransmissao')->nullable();
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
        Schema::table('modulos', function (Blueprint $table) {
            $table->dropColumn('moultimomotivotransmissao');
        });
      }

}

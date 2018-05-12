<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusGrupoMotorista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         if(!Schema::hasColumn('grupo_motorista', 'gmstatus')) {
             Schema::table('grupo_motorista', function (Blueprint $table) {
                   $table->string('gmstatus')->nullable();
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
         Schema::table('grupo_motorista', function (Blueprint $table) {
             $table->dropColumn('gmstatus');
         });
     }
}

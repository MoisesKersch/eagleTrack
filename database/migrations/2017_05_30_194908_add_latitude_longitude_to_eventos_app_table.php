<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatitudeLongitudeToEventosAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eventos_app', function (Blueprint $table) {
              $table->string('jalatitude')->nullable();
              $table->string('jalongitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eventos_app', function (Blueprint $table) {
            $table->dropColumn('jalatitude');
            $table->dropColumn('jalongitude');
        });
    }
}

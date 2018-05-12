<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionCalcularDistanciaEntrePontos extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
        DB::unprepared('CREATE OR REPLACE FUNCTION distancia_m(lat1 NUMERIC, lng1 NUMERIC, lat2 NUMERIC, lng2 NUMERIC)
        RETURNS DOUBLE PRECISION AS
        $BODY$
            SELECT (6371 * acos(
                sin( radians($1) ) * sin( radians( $3 ))
                  + cos( radians($1) ) * cos( radians( $3 )) * cos(radians($4) - radians($2))  )) * 1000
            as distance;
        $BODY$
          LANGUAGE sql IMMUTABLE
          COST 100;');
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      DB::unprepared('DROP PROCEDURE IF EXISTS distancia_m');
  }
}

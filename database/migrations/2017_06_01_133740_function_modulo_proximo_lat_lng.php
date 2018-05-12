<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FunctionModuloProximoLatLng extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
      DB::unprepared('CREATE OR REPLACE FUNCTION buscaModuloProximo(latitude numeric, longitude numeric, id_cliente numeric)
      RETURNS numeric AS
      $$
        declare
          rec RECORD;
          query text;
          menor_dist integer;
          mais_proximo_modulo numeric;
        begin
          menor_dist := 0;
          mais_proximo_modulo := 0;

          query := \'select
                (select distancia_m(\'||$1||\',\'||$2||\',  cast(mo.moultimalat as numeric), cast(mo.moultimalon as numeric)),
                mocodigo
                from
                modulos as mo
                  where moproprietario = \'||$3||\'
                  and select distancia_m(\'||$1||\',\'||$2||\',  cast(mo.moultimalat as numeric), cast(mo.moultimalon as numeric))  < 15\';


            FOR rec IN execute query

            LOOP
            if (rec.distancia_m > menor_dist)
            THEN
              menor_dist := rec.distancia_m;
              mais_proximo_modulo := rec.mocodigo;
            END if;
            END LOOP;
            return mais_proximo_modulo;
          END;

          $$ LANGUAGE plpgsql;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          DB::unprepared('DROP FUNCTION buscaModuloProximo(numeric, numeric, numeric)');
    }
}

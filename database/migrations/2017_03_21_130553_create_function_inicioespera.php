<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionInicioespera extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE OR REPLACE FUNCTION inicioespera(datainicial timestamp, datafinal timestamp)
                         RETURNS TABLE(bilatlog character varying, bidataevento timestamp without time zone, biignicao integer, bicodigo integer, bimotorista integer)
                         LANGUAGE plpgsql
                        AS $function$
                        declare
                            dataini timestamp;
                            nome text;
                            contador integer;
                        begin
                                contador = 0;
                                for bilatlog, bidataevento, biignicao, bicodigo, bimotorista 
                                    in select  bi.bilatlog, bi.bidataevento, bi.biignicao, bi.bicodigo, bi.bimotorista from tbbilhetes bi 
                                        where bi.bidataevento > datainicial 
                                        and  bi.bidataevento <  datafinal
                                        and bi.biignicao = 0
                                        order by bimotorista asc
                                    LOOP
                                        dataini := bidataevento;
                                        if (select bl.biignicao from tbbilhetes bl where bl.bidataevento < dataini order by bl.bidataevento desc limit 1) = 1 THEN
                                            RETURN NEXT;
                                            contador := contador + 1;
                                        END IF;
                                    END LOOP;
                                RETURN;
                            end
                        $function$');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS inicioespera');
    }
}

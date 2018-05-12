<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionIgnicaoLigada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE OR REPLACE FUNCTION public.ignicaoligada(datainicial timestamp without time zone, datafinal timestamp without time zone, idmot integer)
             RETURNS TABLE(bidataevento timestamp without time zone, monome character varying, bicodigo integer, biplaca character varying)
             LANGUAGE plpgsql
            AS $function$
            declare
                dataini timestamp;
                nome text;
                s varchar;
                contador integer;
            begin
                    contador = 0;
                    nome := monome;
                    for bidataevento, monome, bicodigo, biplaca in select  bi.bidataevento, mo.monome, bi.bicodigo, bi.biplaca from tbbilhetes bi 
                            join tbmotorista mo on mo.mocodigo = bi.bimotorista
                            where bi.bidataevento > datainicial 
                            and  bi.bidataevento <  datafinal
                            and bi.biignicao = 1
                            and mo.mocliente = idmot
                            order by bidataevento asc
                        LOOP
                            dataini := bidataevento;
                            if (select bl.biignicao from tbbilhetes bl where bl.bidataevento < dataini order by bl.bidataevento desc limit 1) = 0 THEN
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
        DB::unprepared('DROP PROCEDURE IF EXISTS ignicaoligada');
    }
}

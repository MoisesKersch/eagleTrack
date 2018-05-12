<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProcedureIgnicaoDesligada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = "drop FUNCTION if exists public.ignicaodesligada(integer[], timestamp[])";
         DB::connection()->getPdo()->exec($sql);

        DB::unprepared('CREATE OR REPLACE FUNCTION ignicaodesligada(bicod integer[], datas timestamp without time zone[])
                         RETURNS TABLE(bidataevento timestamp without time zone)
                         LANGUAGE plpgsql
                        AS $function$
                        declare
                            r text;
                            s varchar;
                            i integer;
                        begin
                                i := 1;
                                while (i <= array_upper(bicod, 1))
                                    LOOP
                                        return query select bi.bidataevento from bilhetes bi where bi.bidataevento > datas[i] and bi.biignicao = 0 order by bi.bidataevento asc limit 1;
                                        i := i + 1;
                                    END LOOP;
                                RETURN;
                            END
                        $function$');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         $sql = "drop FUNCTION if exists public.ignicaodesligada(integer[], timestamp[])";
         DB::connection()->getPdo()->exec($sql);
    }
}

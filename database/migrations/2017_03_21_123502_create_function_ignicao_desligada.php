<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionIgnicaoDesligada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE OR REPLACE FUNCTION ignicaodesligada(bicod integer[], datas timestamp[])
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
                                        return query select bi.bidataevento from tbbilhetes bi where bi.bidataevento > datas[i] and bi.biignicao = 0 order by bi.bidataevento asc limit 1;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS ignicaodesligada');
    }
}

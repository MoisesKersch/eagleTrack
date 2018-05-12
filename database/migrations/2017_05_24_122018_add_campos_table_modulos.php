<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposTableModulos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->text('moultimalat')->nullable();
            $table->text('moultimalon')->nullable();
            $table->integer('moultimaignicao')->nullable();
            $table->integer('moultimadirecao')->nullable();
            $table->integer('moultimavelocidade')->nullable();
            $table->dateTimeTz('moultimoevento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->dropColumn('moultimalat');
            $table->dropColumn('moultimalon');
            $table->dropColumn('moultimaignicao');
            $table->dropColumn('moultimadirecao');
            $table->dropColumn('moultimavelocidade');
            $table->dropColumn('moultimoevento');
        });
    }
}



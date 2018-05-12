<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsComecoFimTrabalhoVeiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('veiculos', function (Blueprint $table) {
            if (!Schema::hasColumn('veiculos', 'vehorainiciotrabalho')){
                $table->time('vehorafinaltrabalho')->nullable();
            }
            if (!Schema::hasColumn('veiculos', 'vehorafinaltrabalho')){
                $table->time('vehorainiciotrabalho')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropColumn('vehorafinaltrabalho');
            $table->dropColumn('vehorainiciotrabalho');
        });
    }
}

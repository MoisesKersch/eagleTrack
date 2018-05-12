<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chips', function (Blueprint $table) {
            $table->increments('chcodigo');
            $table->text('chnumero')->nullable();
            $table->integer('choperadora')->nullable();
            $table->integer('chfranquiamb')->nullable();
            $table->integer('chfranquiasms')->nullable();
            $table->decimal('chcusto',10,2)->nullable();
            $table->char('chstatus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chips');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsNameTableEventosApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(Schema::hasTable("eventos_app")){
            Schema::drop("eventos_app");
        }

        if(Schema::hasTable("jornadas_app")){
            Schema::drop("jornadas_app");
        }

        Schema::create('eventos_app', function (Blueprint $table) {
            $table->increments('eacodigo');
            $table->integer('eatipoevento');
            $table->integer('eacodigomotorista')->nullable();
            $table->timestamp('eadataevento')->nullable();
            $table->timestamp('eadataprocessamento')->nullable();
            $table->text('ealatitude',50)->nullable();
            $table->text('ealongitude',50)->nullable();
            $table->timestamps();

            $table->foreign('eacodigomotorista')->references('mtcodigo')
              ->on('motoristas')
              ->onDelete('cascade');

            $table->foreign('eatipoevento')->references('id')
              ->on("tipo_evento_app")
              ->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}

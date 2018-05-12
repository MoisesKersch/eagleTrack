<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLinhas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linhas', function (Blueprint $table) {
            $table->increments('licodigo');
            $table->text('lidescricao')->nullable();
            $table->integer('livelocidademedia')->nullable();
            $table->integer('lidistancia')->nullable();
            $table->timestamp('litempoestimado')->nullable();
            $table->boolean('liseguirordeminsercao')->default(false);
            $table->integer('licliente')->nullable();
            $table->text('lirotaosrm')->nullable();
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
        // Schema::table('linhas', function (Blueprint $table) {
        //     $table->dropColumn('licodigo');
        //     $table->dropColumn('lidescricao');
        //     $table->dropColumn('livelocidademedia');
        //     $table->dropColumn('lidistancia');
        //     $table->dropColumn('livelocidademedia');
        //     $table->dropColumn('litempoestimado');
        //     $table->dropColumn('liseguirordeminsercao');
        //     $table->dropColumn('licliente');
        //     $table->dropColumn('lirotaosrm');
        // });
        Schema::dropIfExists('linhas');
    }
}

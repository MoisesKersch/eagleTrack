<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNameJornadasAppToEventosApp extends Migration
{
  /**
  	 * Run the migrations.
  	 *
  	 * @return void
  	 */
  	public function up()
  	{
      if (Schema::hasTable('jornadas_app'))
  		  Schema::rename('jornadas_app', 'eventos_app');
  	}

  	/**
  	 * Reverse the migrations.
  	 *
  	 * @return void
  	 */
  	public function down()
  	{
      if (Schema::hasTable('eventos_app'))
  		  Schema::rename('eventos_app', 'jornadas_app');
  	}
}

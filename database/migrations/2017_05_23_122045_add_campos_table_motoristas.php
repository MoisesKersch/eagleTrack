<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposTableMotoristas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('motoristas', function (Blueprint $table) {
            if (!Schema::hasColumn('motoristas', 'mtcpf'))
              $table->string('mtcpf', 14)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtrg'))
              $table->string('mtrg', 18)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtdatanasc'))
              $table->string('mtdatanasc', 10)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtcnh'))
              $table->string('mtcnh', 2)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtcnhvalidade'))
              $table->string('mtcnhvalidade', 10)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtcnhnumero'))
              $table->string('mtcnhnumero', 18)->nullable();

            if (!Schema::hasColumn('motoristas', 'mttelefone'))
              $table->string('mttelefone', 18)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtstatus'))
              $table->string('mtstatus', 4)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtperfil'))
              $table->string('mtperfil', 2)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtgrupo'))
              $table->integer('mtgrupo')->unsigned()->nullable();

            $table->foreign('mtgrupo')->references('gmcodigo')->on('grupo_motorista')->onDelete('cascade');
            if (!Schema::hasColumn('motoristas', 'mtendereco'))
              $table->string('mtendereco', 200)->nullable();

            if (!Schema::hasColumn('motoristas', 'mtraio'))
              $table->integer('mtraio')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('motoristas', function (Blueprint $table) {
            $table->dropColumn('mtcpf');
            $table->dropColumn('mtrg');
            $table->dropColumn('mtdatanasc');
            $table->dropColumn('mtcnh');
            $table->dropColumn('mtcnhvalidade');
            $table->dropColumn('mtcnhnumero');
            $table->dropColumn('mttelefone');
            $table->dropColumn('mtstatus');
            $table->dropColumn('mtperfil');
            $table->dropColumn('mtgrupo');
            $table->dropColumn('mtendereco');
            $table->dropColumn('mtraio');
        });
    }
}

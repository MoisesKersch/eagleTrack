<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposToMotoristaAjudanteTable extends Migration
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
            $table->text("mtrg")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtdatanasc'))
            $table->date("mtdatanasc")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtcnh'))
            $table->text("mtcnh")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtcnhvalidade'))
            $table->date("mtcnhvalidade")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtcnhnumero'))
            $table->text("mtcnhnumero")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtendereco'))
            $table->text("mtendereco")->nullable();

          if (!Schema::hasColumn('motoristas', 'mttelefone'))
            $table->text("mttelefone")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtstatus'))
            $table->text("mtstatus")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtperfil'))
            $table->text("mtperfil")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtlatitude'))
            $table->float("mtlatitude")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtlongitude'))
            $table->float("mtlongitude")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtgrupo'))
            $table->integer("mtgrupo")->nullable();

          if (!Schema::hasColumn('motoristas', 'mtraio'))
            $table->integer("mtraio")->nullable();

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
            $table->dropColumn("mtcpf");
            $table->dropColumn("mtrg");
            $table->dropColumn("mtdatanasc");
            $table->dropColumn("mtcnh");
            $table->dropColumn("mtcnhvalidade");
            $table->dropColumn("mtcnhnumero");
            $table->dropColumn("mtendereco");
            $table->dropColumn("mttelefone");
            $table->dropColumn("mtstatus");
            $table->dropColumn("mtperfil");
            $table->dropColumn("mtlatitude");
            $table->dropColumn("mtlongitude");
            $table->dropColumn("mtgrupo");
            $table->dropColumn("mtraio");
        });
    }
}

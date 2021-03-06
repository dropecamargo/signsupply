<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('empresa_tercero')->unsigned();
            $table->integer('empresa_niif')->nullable()->comment = '1 - Plena, 2 - Pymes, 3 - Micro pymes';
            $table->string('empresa_cc_contador', 15)->nullable();
            $table->string('empresa_tj_contador', 15)->nullable();
            $table->string('empresa_nm_contador', 200)->nullable();
            $table->string('empresa_cc_revisor', 15)->nullable();
            $table->string('empresa_tj_revisor', 15)->nullable();
            $table->string('empresa_nm_revisor', 200)->nullable();
            $table->integer('empresa_cuentacartera')->unsigned();
            $table->date('empresa_fecha_cierre_contabilidad');
            $table->date('empresa_fecha_cierre');
            $table->integer('empresa_iva')->unsigned();
            $table->boolean('empresa_pedidoc')->default(false);

            $table->foreign('empresa_tercero')->references('id')->on('tercero')->onDelete('restrict');
            $table->foreign('empresa_cuentacartera')->references('id')->on('plancuentas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa');
    }
}

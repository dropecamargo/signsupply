<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePedidoc1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidoc1', function(Blueprint $table){
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('pedidoc1_sucursal')->unsigned();
            $table->integer('pedidoc1_numero')->unsigned();
            $table->date('pedidoc1_fecha');
            $table->integer('pedidoc1_documentos')->unsigned();
            $table->integer('pedidoc1_tercero')->unsigned();
            $table->integer('pedidoc1_contacto')->unsigned();
            $table->integer('pedidoc1_factura1')->unsigned()->nullable();
            $table->integer('pedidoc1_cuotas')->unsigned();
            $table->integer('pedidoc1_plazo')->unsigned();
            $table->date('pedidoc1_primerpago');
            $table->integer('pedidoc1_vendedor')->unsigned();
            $table->double('pedidoc1_bruto')->default(0);
            $table->double('pedidoc1_descuento')->default(0);
            $table->double('pedidoc1_iva')->default(0);
            $table->double('pedidoc1_retencion')->default(0);
            $table->double('pedidoc1_total')->default(0);
            $table->text('pedidoc1_observaciones');
            $table->string('pedidoc1_autorizacion_ca', 40)->nullable();
            $table->string('pedidoc1_autorizacion_co', 40)->nullable();
            $table->boolean('pedidoc1_anular')->default(0);
            $table->integer('pedidoc1_usuario_elaboro')->unsigned();
            $table->datetime('pedidoc1_fh_elaboro');
            $table->integer('pedidoc1_usuario_anulo')->unsigned()->nullable();
            $table->datetime('pedidoc1_fh_anulo');

            $table->foreign('pedidoc1_sucursal')->references('id')->on('sucursal')->onDelete('restrict');
            $table->foreign('pedidoc1_documentos')->references('id')->on('documentos')->onDelete('restrict');
            $table->foreign('pedidoc1_tercero')->references('id')->on('tercero')->onDelete('restrict');
            $table->foreign('pedidoc1_contacto')->references('id')->on('tcontacto')->onDelete('restrict');
            $table->foreign('pedidoc1_vendedor')->references('id')->on('tercero')->onDelete('restrict');
            $table->foreign('pedidoc1_usuario_elaboro')->references('id')->on('tercero')->onDelete('restrict');
            $table->foreign('pedidoc1_usuario_anulo')->references('id')->on('tercero')->onDelete('restrict');

            $table->unique(['pedidoc1_sucursal', 'pedidoc1_numero'], 'pedidoc1_sucursal_numero_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidoc1');
    }
}

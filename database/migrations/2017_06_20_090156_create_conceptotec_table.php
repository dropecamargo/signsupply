<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptotecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conceptotec', function(Blueprint $table){
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('conceptotec_nombre', 50)->unique();
            $table->integer('conceptotec_cuenta')->unsigned();
            $table->boolean('conceptotec_activo')->default(false);

            $table->foreign('conceptotec_cuenta')->references('id')->on('plancuentas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conceptotec');
    }
}

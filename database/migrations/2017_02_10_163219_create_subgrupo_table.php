<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubgrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subgrupo', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('subgrupo_codigo', 4)->unique();
            $table->string('subgrupo_nombre', 100);
            $table->boolean('subgrupo_activo')->default(false);
            $table->integer('subgrupo_retefuente')->unsigned()->nullable();

            $table->foreign('subgrupo_retefuente')->references('id')->on('retefuente')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subgrupo');
    }
}

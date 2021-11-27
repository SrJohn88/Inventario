<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioRevisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventario_revision', function (Blueprint $table) {
            
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->unsignedInteger('inventario_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');

            $table->unsignedInteger('revision_id');
            $table->foreign('revision_id')->references('id')->on('revisiones');

            $table->boolean('revisado')->nullable();
            $table->boolean('esCorrecto')->nullable();
            $table->text('observacion')->nullable();

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
        Schema::dropIfExists('inventario_revision');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioMovimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventario_movimiento', function (Blueprint $table) {
            $table->engine = 'InnoDb';

            $table->increments('id');

            $table->unsignedInteger('inventario_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');

            $table->unsignedInteger('movimiento_id');
            $table->foreign('movimiento_id')->references('id')->on('movimientos');

            $table->String('falla')->nullable();
            $table->string('observaciones')->nullable();
            
            $table->string('usuario', 300)->nullable();
           
            $table->boolean('recibido')->default( false );
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
        Schema::dropIfExists('inventario_movimiento');
    }
}

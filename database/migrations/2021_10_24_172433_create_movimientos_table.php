<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
           
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->unsignedInteger('tipo_id');
            $table->foreign('tipo_id')->references('id')->on('tipo_movimientos');
            
            $table->unsignedIntener('recibido_por');
            $table->foreign('recibido_por')->references('id')->on('empleados');

            $table->unsignedIntener('aprobado_por');
            $table->foreign('aprobado_por')->references('id')->on('empleados');
            
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->string('descripcion', 200 )->nullable();

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
        Schema::dropIfExists('movimientos');
    }
}

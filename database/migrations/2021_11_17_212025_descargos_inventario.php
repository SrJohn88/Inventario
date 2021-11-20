<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DescargosInventario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descargo_inventario', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            
            $table->unsignedInteger('descargo_id');
            $table->foreign('descargo_id')->references('id')->on('descargos');

            $table->unsignedInteger('inventario_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');

            $table->string('observacion', 300)->nullable();
        
            $table->boolean('eliminado')->default(false);

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
        Schema::dropIfExists('descargo_inventario');        
    }
}

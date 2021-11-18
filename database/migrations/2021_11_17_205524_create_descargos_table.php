<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descargos', function (Blueprint $table) {
            
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            
            $table->unsignedInteger('tipoDescargo_id');
            $table->foreign('tipoDescargo_id')->references('id')->on('tipo_descargos');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('observacion', 500)->nullable();

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
        Schema::dropIfExists('descargos');
    }
}

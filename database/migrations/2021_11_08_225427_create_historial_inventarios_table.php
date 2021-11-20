<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialInventariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_inventarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->unsignedInteger('inventario_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');

            $table->string('codigo',50);
            $table->string('serie',100)->nullable();
            $table->string('descripcion',250);
            
            $table->unsignedInteger('marca_id')->nullable();
            $table->foreign('marca_id')->references('id')->on('marcas');

            $table->string('modelo',100)->nullable();
            
            $table->unsignedInteger('procedencia_id');
            $table->foreign('procedencia_id')->references('id')->on('procedencias');

            $table->unsignedInteger('entidad_id')->nullable();
            $table->foreign('entidad_id')->references('id')->on('entidades');

            $table->unsignedInteger('cuenta_id')->nullable();
            $table->foreign('cuenta_id')->references('id')->on('cuentas');

            $table->double('precio',10, 2)->nullable();

            $table->unsignedInteger('rubro_id');
            $table->foreign('rubro_id')->references('id')->on('rubros');

            $table->unsignedInteger('ubicacion_id');
            $table->foreign('ubicacion_id')->references('id')->on('ubicaciones');

            $table->text('desUbicacion')->nullable();
            $table->date('fecha_adquision')->nullable();
            $table->text('observacion',250)->nullable();

            $table->unsignedInteger('estado_id')->default(1);
            $table->foreign('estado_id')->references('id')->on('estados');

            $table->Boolean('eliminado')->default( false );
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
        Schema::dropIfExists('historial_inventarios');
    }
}

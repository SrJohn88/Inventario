<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estado;
use App\Models\Cargo;
use App\Models\TipoMovimiento;


class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estado1 = new Estado();
        $estado1->estado = 'DISPONIBLE';
        $estado1->save();

        $estado2 = new Estado();
        $estado2->estado = 'EN MANTENIMIENTO';
        $estado2->save();

        $estado3 = new Estado();
        $estado3->estado = 'A PRESTAMO';
        $estado3->save();

        $estado4 = new Estado();
        $estado4->estado = 'SALIDA';
        $estado4->save();

        $estado5 = new Estado();
        $estado5->estado = 'FUERA DE INVENTARIO';
        $estado5->save();

        $cargo1 = new Cargo();
        $cargo1->cargo = 'DOCENTE';
        $cargo1->save();

        $cargo2 = new Cargo();
        $cargo2->cargo = 'SECRETARIA';
        $cargo2->save();

        $tipo2 = new TipoMovimiento();
        $tipo2->tipo = 'PRÉSTAMO';
        $tipo2->save();

        $tipo3 = new TipoMovimiento();
        $tipo3->tipo = 'TRANSLADO INTERNO';
        $tipo3->save();

        $tipo4 = new TipoMovimiento();
        $tipo4->tipo = 'SALIDA POR REPARACIÓN';
        $tipo4->save();

        $tipo4 = new TipoMovimiento();
        $tipo4->tipo = 'SALIDA';
        $tipo4->save();
    }
}

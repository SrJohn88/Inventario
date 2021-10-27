<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empleado;


class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $empleado1 = new Empleado();
        $empleado1->nombre = 'Leonel Andres';
        $empleado1->apellido = 'Messi';
        $empleado1->cargo_id = 1;
        $empleado1->save();
    }
}

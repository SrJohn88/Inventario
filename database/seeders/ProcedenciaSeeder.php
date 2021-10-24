<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Procedencia;

class ProcedenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedencia1 = new Procedencia();
        $procedencia1->procedencia = 'COMPRAS';
        $procedencia1->save();

        $procedencia2 = new Procedencia();
        $procedencia2->procedencia = 'DONACIONES';
        $procedencia2->save();

        $procedencia3 = new Procedencia();
        $procedencia3->procedencia = 'OTROS INVENTARIOS';
        $procedencia3->save();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    function crear() 
    {
        return view('Inventario.movimientos.crear');
    }

    function save (  ) 
    {
        try {

            DB::beginTransaction();

            $movimiento = new Movimiento();
            $movimiento->tipo_id = 1;
            $movimiento->recibido_por = 1;
            $movimiento->aprobado_por = 1;
            $movimiento->user_id = 1;
            $movimiento->descripcion = 'PRUEBA DESDE LARAVEL new';
            $movimiento->save();

            $movimiento->inventario()->attach( 1, [
                'fallas' => 'Sin fallas', 'observaciones' => 'sin observacions'
            ]);

            DB::commit();

        } catch ( \Exception $e )
        {
            DB::rollBack();
            return $e->getMessage();
        }
        //}
    }
}

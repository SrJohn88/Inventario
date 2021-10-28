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

    function index()
    {
        return view( 'Inventario.movimientos.index' );
    }

    function obtenerMovimientos()
    {
        return response()->json([
            'movimientos' => Movimiento::with('tipoMovimiento', 'recibe', 'aprueba', 'user')->get()
        ]);
    }

    function save ( Request $request ) 
    {
        try {

            if( $request->isMethod('POST') )
            {   
                DB::beginTransaction();

                $validacion = Validator::make($request->all(), [
                    
                ]);

                if ($validacion->fails()) {
                    return response()->json([
                        'respuesta' => false,
                        'mensaje' => '',
                        'inventario' => $validacion->errors()->get('codigo')
                    ]);
                }

                $movimiento = new Movimiento();
                $movimiento->tipo_id = $request->input('tipoMovimiento.id');
                $movimiento->recibido_por = $request->input('recibe.id');
                $movimiento->aprobado_por = $request->input('aprueba.id');
                $movimiento->user_id = \Auth::user()->id;
                $movimiento->descripcion = $request->input('observacion');
                $movimiento->save();

                // [ { inventario_id : 1, falla: 'sin fallar', obervaciones: 'sin observaciones' } ]

                $detalleMovimiento = $request->input('activos');               
                $movimiento->inventario()->attach( $detalleMovimiento  );

                DB::commit();

                return response()->json([
                    'respuesta' => true,
                    'mensaje' => 'Movimiento registrado con exito'
                ]);

            }        

        } catch ( \Exception $e )
        {
            DB::rollBack();
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'Ocurrio un error en el servidor'
            ]);
        }
        //}
    }
}

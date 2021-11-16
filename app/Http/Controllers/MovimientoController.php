<?php

namespace App\Http\Controllers;

use App\Models\HistorialInventario;
use App\Models\Inventario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\HistorialMovimiento;
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
                    'recibe.id' => 'required',
                    'tipoMovimiento.id' => 'required',
                    'aprueba.id' => 'required'
                ]);

                if ($validacion->fails()) {
                    return response()->json([
                        'respuesta' => false,
                        'mensaje' => '',
                        'errors' => $validacion->errors()
                    ]);
                }

                $movimiento = new Movimiento();
                $movimiento->tipo_id = $request->input('tipoMovimiento.id');
                $movimiento->recibido_por = $request->input('recibe.id');
                $movimiento->aprobado_gerencia = $request->input('gerencia.id');
                $movimiento->aprobado_por = $request->input('aprueba.id');
                $movimiento->user_id = \Auth::user()->id;
                $movimiento->seTranslada = $request->input('ubicacion.id');
                $movimiento->descripcion = $request->input('observacion');
                $movimiento->save();

                // [ { inventario_id : 1, falla: 'sin fallar', obervaciones: 'sin observaciones' } ]

                $detalleMovimiento = $request->input('activos');               
                $movimiento->inventario()->attach( $detalleMovimiento  );

                foreach ( $request->input('activos') as $value )
                {               
                    if ( $movimiento->tipo_id == 1 ) 
                    {
                        $inv = Inventario::find( $value['inventario_id'] );

                        $valorAnterior = $inv->estado->estado;

                        $inv->estado_id = 3;
                        $inv->save();

                        $historial = new HistorialMovimiento();
                        $historial->inventario_id = $inv->id;
                        $historial->campo = 'Estado';
                        $historial->valor_anterior = $valorAnterior;
                        $historial->valor_nuevo = 'A PRESTAMO';
                        $historial->save();


                    } else if (  $movimiento->tipo_id == 2 )
                    {
                        $inv = Inventario::find( $value['inventario_id'] );
                        
                        $valorAnterior = $inv->ubicacion->ubicacion;

                        $inv->ubicacion_id = $request->input('ubicacion.id');
                        $inv->save();

                        $historial = new HistorialMovimiento();
                        $historial->inventario_id = $inv->id;
                        $historial->campo = 'Ubicacion';
                        $historial->valor_anterior = $valorAnterior;
                        $historial->valor_nuevo = $request->input('ubicacion.ubicacion');
                        $historial->save();

                    } else if ( $movimiento->tipo_id == 3 )
                    {
                        $inv = Inventario::find( $value['inventario_id'] );
                        $valorAnterior = $inv->estado->estado;
                        $inv->estado_id = 2;
                        $inv->save();

                        $historial = new HistorialMovimiento();
                        $historial->inventario_id = $inv->id;
                        $historial->campo = 'Estado';
                        $historial->valor_anterior = $valorAnterior;
                        $historial->valor_nuevo = 'EN MANTENIMIENTO';
                        $historial->save();

                    } else if ( $movimiento->tipo_id == 4 )
                    {
                        $inv = Inventario::find( $value['inventario_id'] );
                        $valorAnterior = $inv->estado->estado;
                        $inv->estado_id = 4;
                        $inv->save();

                        $historial = new HistorialMovimiento();
                        $historial->inventario_id = $inv->id;
                        $historial->campo = 'Estado';
                        $historial->valor_anterior = $valorAnterior;
                        $historial->valor_nuevo = 'SALIDA';
                        $historial->save();
                    }
                }

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

    function updateMovimiento( Request $request, Movimiento $movimiento )
    {
        
        $validacion = Validator::make($request->all(), [
                    
        ]);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'inventario' => $validacion->errors()->get('')
            ]);
        }
    
    
        foreach ( $request->input('activos') as $value )
        {
            $movimiento->inventario()->updateExistingPivot( $value['inventario_id'], 
            [
                'falla' => $value['falla'], 
                'observaciones' => $value['observaciones'],
                'recibido' => true,
                'usuario' => \Auth::user()->name
            ]);
        }

        foreach ( $request->input('activos') as $value )
        {
            $inv = Inventario::find( $value['inventario_id'] );
            $valorAnterior = $inv->estado->estado;
            $inv->estado_id = 1;
            $inv->save();

            $historial = new HistorialMovimiento();
            $historial->inventario_id = $inv->id;
            $historial->campo = 'Estado';
            $historial->valor_anterior = $valorAnterior;
            $historial->valor_nuevo = 'DISPONIBLE';
            $historial->save();

        }
            
        return response()->json([
            'respuesta' => true,
            'mensaje' => 'Movimiento actualizado con exito',
        ]);
            
    }

    function obtenerDetMovimiento( Movimiento $movimiento )
    {
        return response()->json([
            'movimiento' => Movimiento::with('tipoMovimiento', 'recibe', 'aprueba', 'aprobadoGerencia', 'user', 'inventario', 'inventario.marca')
                            ->where('id', $movimiento->id )->get()
        ]);
    }

    function detalleMovimiento()
    {
        return view('Inventario.movimientos.actualizar');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Descargo;
use App\Models\HistorialMovimiento;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DescargoController extends Controller
{
    protected $mensajes = [
        'required' => 'Este campo es requerido',
        'min' => 'Este campo debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index ()
    {
        return view('Inventario.descargos.index');
    }

    function crear ()
    {
        return view('Inventario.descargos.crear');
    }

    function save ( Request $request )
    {
        try {

            if ( $request->isMethod('POST') )
            {
                DB::beginTransaction();

                $validacion = Validator::make($request->all(), [
                    'tipoDescargo.id' => 'required',
                    'fecha' => 'required',
                    'acta' => 'required'
                ], $this->mensajes);

                if ($validacion->fails()) {
                    return response()->json([
                        'respuesta' => false,
                        'mensaje' => '',
                        'errors' => $validacion->errors()
                    ]);
                }
                
                $descargo = new Descargo();
                $descargo->tipoDescargo_id = (int) $request->input('tipoDescargo.id');
                $descargo->user_id = \Auth::user()->id;
                $descargo->observacion = $request->input('observaciones');
                $descargo->fechaActa = $request->input('fecha');
                $descargo->acta = $request->input('acta');
                $descargo->save();

                foreach( $request->input('activo') as $value )
                {
                    $descargo->inventario()->attach( $value['inventario_id'], [
                        'observacion' => $value['observaciones']
                    ]);

                    $inventario = Inventario::find( $value['inventario_id'] );
                    
                    $valorAnterior = $inventario->estado->estado;

                    $inventario->estado_id = 5;
                    $inventario->eliminado = true;
                    $inventario->save();

                    $historial = new HistorialMovimiento();
                    $historial->inventario_id = $inventario->id;
                    $historial->campo = 'Descargo';
                    $historial->valor_anterior = $valorAnterior;
                    $historial->valor_nuevo = 'FUERA DE INVENTARIO';
                    $historial->save();
                }
                

                DB::commit();

                return response()->json([
                    'respuesta' => true,
                    'mensaje' => 'Se realizo el descargo exitosamente'
                ]);
            } else 
            {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => 'Peticion no autorizada, metodo POST'
                ]);
            }

        } catch ( \Exception $e )
        {
            DB::rollBack();
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'Ocurrio un error en el servidor',
                'excepcion' => $e->getMessage()
            ]);
        }
    }

    function obtenerDescargos()
    {
        return response()->json([
            'descargos' => Descargo::with('tipoDescargo', 'user', 'inventario')->get()
        ]);
    }

    function obtenerDescargo( Descargo $descargo )
    {
        return response()->json([
            'descargo' => $descargo::with('tipoDescargo', 'user', 'inventario', 'inventario.marca')->firstOrFail()
        ]);
    }

}

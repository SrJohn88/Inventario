<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Ubicacion;

class UbicacionController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index()
    {
        return view('Ubicaciones.index');
    }

    function getUbicaciones()
    {
        return response()->json([
            'ubicaciones' => Ubicacion::all() 
        ]);
    }

    function save( Request $request )
    {
        if ($request->isMethod('POST')) {
            
            $validacion = Validator::make($request->all(), [
                'ubicacion' => 'required|unique:ubicaciones|min:2|max:200',
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'ubicacion' => $validacion->errors()->get('ubicacion')
                ]);
            }

            $ubicacion = new Ubicacion();
            $ubicacion->ubicacion = $request->get('ubicacion');
            $ubicacion->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'La ubicacion ha sido agregado exitosamente',
                'ubicacion' => [ 'id' => $ubicacion->id, 'ubicacion' => $ubicacion->ubicacion, 'eliminado' => 0 ]
            ]);
        }
    }

    function update(Request $request, Ubicacion $ubicacion )
    {
        $validacion = Validator::make($request->all(), [
            'ubicacion' => 'required|unique:ubicaciones,ubicacion,'.$ubicacion->id.'|min:2|max:100'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'ubicacion' => $validacion->errors()->get('ubicacion')
            ]);
        } else 
        {
            $ubicacion->ubicacion = $request->get('ubicacion');
            $ubicacion->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'La ubicacion ha sido actualizado con exito'
            ]);
        }

    }

    function delete( Ubicacion $ubicacion, $accion )
    {
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {
            $contador = $ubicacion->inventario->count();
        }

        if( $contador == 0)
        {
            $ubicacion->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
            $ubicacion->save();

            $mensaje = $accion ? 'El rubro '. $ubicacion->ubicacion.' ha sido desactivada con exito' : 'El rubro '. $ubicacion->ubicacion.' ha sido restaurado con exito';
            
            return response()->json([
                'respuesta' => true,
                'mensaje' => $mensaje
            ]);
        } else 
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar la ubicacion, debido que hay registros que hacen referencia a ella'
            ]);
        }
        
    }
}

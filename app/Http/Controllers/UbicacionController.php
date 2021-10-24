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
                'mensaje' => 'La ubicacion ha sido agregado exitosamente'
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
                'mensaje' => $validacion->errors()->get('ubicacion')
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
        $ubicacion->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
        $ubicacion->save();

        $mensaje = $accion ? 'El rubro '. $ubicacion->ubicacion.' ha sido eliminada con exito' : 'El rubro '. $ubicacion->ubicacion.' ha sido restaurado con exito';
        
        return response()->json([
            'respuesta' => true,
            'mensaje' => $mensaje
        ]);
    }
}

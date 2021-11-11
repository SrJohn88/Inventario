<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class EntidadController extends Controller
{
    function index()
    {
        return view('entidades.entidades');
    }

    function obtenerEntidades()
    {
        $entidades = DB::table('entidades')
            ->select('id', 'entidad', 'eliminado')
            ->get();
        return response()->json([
            'entidades' => $entidades
        ]);
    }

    private $mensaje = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function save(Request $request)
    {
        if ($request->isMethod('POST')) {
            
            $validacion = Validator::make($request->all(), [
                'entidad' => 'required|unique:entidades|min:2|max:50'
            ], $this->mensaje);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'entidad' => $validacion->errors()->get('entidad')
                ]);
            }

            $entidad = new Entidad();
            $entidad->entidad = $request->get('entidad');
            $entidad->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'La entidad ha sido agregada exitosamente'
            ]);
        }
    }

    function update(Request $request, Entidad $entidad)
    {
        $validacion = Validator::make($request->all(), [
            'entidad' => 'required|unique:entidades|min:2|max:50'
        ], $this->mensaje);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'entidad' => $validacion->errors()->get('entidad')
            ]);
        }

        $entidad->entidad = $request->input('entidad');
        $entidad->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'La entidad ha sido actualizada con exito'
        ]);
    }

    function delete ( Entidad $entidad, $accion ) 
    {
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {
            $contador = $entidad->inventario->count();
        }

        if( $contador == 0)
        {
            $entidad->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
            $entidad->save();

            $mensaje = $accion ? 'La entidad '.$entidad->entidad.' fue desactivada con exito' : 'La entidad '.$entidad->entidad.' fue activada con exito';

            return response()->json([
                'respuesta' => true,
                'mensaje' => $mensaje
            ]);
        } else 
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar la entidad, debido que hay registros que hacen referencia a ella'
            ]);
        }
    }
}

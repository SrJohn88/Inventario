<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CargoController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index()
    {
        return view('Empleados.cargo');
    }

    function obtenerCargos() 
    {
        return response()->json([
            'cargos' => Cargo::all()
        ]);
    }

    function save( Request $request )
    {
        if ($request->isMethod('POST')) 
        {            
            $validacion = Validator::make($request->all(), [
                'cargo' => 'required|unique:cargos|min:2|max:50'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'cargo' => $validacion->errors()->get('cargo')
                ]);
            }

            $cargo = new Cargo();
            $cargo->cargo = $request->input('cargo');
            $cargo->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El cargo ha sido agregado exitosamente',
                'cargo' => [ 'id' => $cargo->id, 'cargo' => $cargo->cargo, 'eliminado' => 0]
            ]);
        }
    }

    function update( Request $request, Cargo $cargo )
    {
        $validacion = Validator::make($request->all(), [
            'cargo' => 'required|unique:cargos,cargo,'.$cargo->id.'|min:2|max:50'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'cargo' => $validacion->errors()->get('cargo')
            ]);
        }

        $cargo->cargo = $request->input('cargo');
        $cargo->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'El cargo ha sido agregado exitosamente',
            'cargo' => [ 'id' => $cargo->id, 'cargo' => $cargo->cargo, 'eliminado' => 0]
        ]);


    }

    function delete( Cargo $cargo, $accion )
    {
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {
            $contador = $cargo->empleados->count();
        }

        if ( $contador == 0)
        {
            $cargo->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
            $cargo->save();

            $mensaje = $accion ? 'El cargo '. $cargo->cargo.' ha sido desactivado con exito' : 'El cargo '. $cargo->cargo.' ha sido restaurado con exito';
            
            return response()->json([
                'respuesta' => true,
                'mensaje' => $mensaje
            ]);
        } else 
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar el cargo, debido que hay registros que hacen referencia a el'
            ]);
        }
    }
}

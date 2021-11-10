<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index()
    {
        return view('Empleados.index');
    }

    function obtenerEmpleados() 
    {
        return response()->json([
            'empleados' => Empleado::with('cargo')->get()
        ]);
    }

    function save( Request $request )
    {
        if( $request->isMethod('POST') )
        {   
            $validacion = Validator::make($request->all(), [
                'dui' => 'required|unique:empleados|max:10',
                'nombre' => 'required|min:2|max:200',
                'apellido' => 'required|min:2|max:200'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'errors' => [
                        'dui' => $validacion->errors()->get('dui'),
                        'nombre' => $validacion->errors()->get('nombre'),
                    ]
                    
                ]);
            }

            $empleado = new Empleado();
            $empleado->nombre = $request->input('nombre');
            $empleado->apellido = $request->input('apellido');
            $empleado->dui = $request->input('dui');
            $empleado->cargo_id = $request->input('cargo.id');
            $empleado->save();
        
            return response()->json([
                'respuesta' => true,
                'mensaje' => 'Empleado registrado con exito',
                'empleado' => $empleado
            ]);
        }
    }

    function update(Request $request, Empleado $empleado )
    {
        $validacion = Validator::make($request->all(), [
            'dui' => 'required|unique:empleados,dui,'.$empleado->id.'|min:2|max:100'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'dui' => $validacion->errors()->get('dui')
            ]);
        }

        $empleado->nombre = $request->input('nombre');
        $empleado->apellido = $request->input('apellido');
        $empleado->dui = $request->input('dui');
        $empleado->cargo_id = $request->input('cargo.id');
        $empleado->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'Empleado registrado con exito',
            'empleado' => $empleado
        ]);

    }

    function delete(Empleado $empleado, $accion )
    {
        $empleado->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
        $empleado->save();

        $mensaje = filter_var($accion, FILTER_VALIDATE_BOOLEAN) 
                        ? 'El usuario'. $empleado->nombre .' desactivado con exito' 
                        : 'El usuario'. $empleado->nombre .' ha sido activado con exito';

        return response()->json([
            'respuesta' => true,
            'mensaje' => $mensaje
        ]);
        
    }
}

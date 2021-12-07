<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    
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
                'nombre' => 'required|min:2|max:200',
                'apellido' => 'required|min:2|max:200',
                'cargo.id' => 'required'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'errors' => [
                        'nombre' => $validacion->errors()->get('nombre'),
                        'apellido' => $validacion->errors()->get('apellido'),
                        'cargo' =>   $validacion->errors()->get('cargo.id')
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
            'nombre' => 'required|min:2|max:200',
            'apellido' => 'required|min:2|max:200',
            'cargo.id' => 'required',            
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
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {   
            if ( $empleado->recibidoPor->count() > 0 
                        || $empleado->aprobadoPor->count() > 0 
                        || $empleado->aprobadoGerencia->count() > 0 )
                        {
                            $contador = 1;
                        }
        }

        if ( $contador == 0 )
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
        } else
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar el empleado, debido que hay registros que hacen referencia a ella'
            ]);
        }
 
    }
}

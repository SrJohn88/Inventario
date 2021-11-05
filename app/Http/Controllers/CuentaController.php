<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuenta;

use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index()
    {
        return view('Cuentas.index');
    }

    function getCuentas()
    {
        return response()->json([
            'cuentas' => Cuenta::all()
        ]); 
    }

    function save(Request $request)
    {
        if ($request->isMethod('POST')) {
            
            $validacion = Validator::make($request->all(), [
                'cuenta' => 'required|unique:cuentas|min:2|max:100'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'cuenta' => $validacion->errors()->get('cuenta')
                ]);
            }

            $cuenta = new Cuenta();
            $cuenta->cuenta = $request->get('cuenta');
            $cuenta->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'La cuenta ha sido agregado exitosamente',
                'cuenta' => ['id' => $cuenta->id , 'cuenta' => $cuenta->cuenta, 'eliminado' => 0 ]
            ]);
        }
    }

    function update(Request $request, Cuenta $cuenta )
    {
        $validacion = Validator::make($request->all(), [
            'cuenta' => 'required|unique:cuentas,cuenta,'.$cuenta->id.'|min:2|max:100'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'cuenta' => $validacion->errors()->get('cuenta')
            ]);
        } else 
        {
            $cuenta->cuenta = $request->input('cuenta');
            $cuenta->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'La cuenta ha sido actualizado con exito'
            ]);
        }

    }

    function delete( Cuenta $cuenta, $accion )
    {
        $cuenta->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
        $cuenta->save();

        $mensaje = filter_var($accion, FILTER_VALIDATE_BOOLEAN) 
                        ? 'La cuenta ha sido desactivada con exito' 
                        : 'La cuenta ha sido activada con exito';

        return response()->json([
            'respuesta' => true,
            'mensaje' => $mensaje
        ]);

    }
}

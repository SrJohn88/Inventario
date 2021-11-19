<?php

namespace App\Http\Controllers;

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
                ], $this->mensajes);

                if ($validacion->fails()) {
                    return response()->json([
                        'respuesta' => false,
                        'mensaje' => '',
                        'errors' => $validacion->errors()
                    ]);
                }
                
                
            }

        } catch ( \Exception $e )
        {
            DB::rollBack();
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'Ocurrio un error en el servidor'
            ]);
        }
    }

}

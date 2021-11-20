<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoDescargos;


class TipoDescargosController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index ()
    {
        return view('Inventario.descargos.tiposDescargos');
    }

    function obtenerTiposDescargos()
    {
        return response()->json([
            'tiposDescargos' => TipoDescargos::all()      
        ]);
    }

    function save (Request $request )
    {
        if ( $request->isMethod('POST') ) 
        {
            $validacion = Validator::make($request->all(), [
                'tipoDescargo' => 'required|unique:tipo_descargos|min:2|max:200',
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'tipoDescargo' => $validacion->errors()->get('tipoDescargo')
                ]);
            }

            $tipo = new TipoDescargos();
            $tipo->tipoDescargo = $request->input('tipoDescargo');
            $tipo->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El tipo de descargo fue agregado con exito',
                'tipoDescargo' => [ 'id' => $tipo->id, 'tipoDescargo' => $tipo->tipoDescargo, 'eliminado' => 0 ]
            ]);
        }
    }

    function update ( Request $request, TipoDescargos $tipoDescargo )
    {
        $validacion = Validator::make($request->all(), [
            'tipoDescargo' => 'required|unique:tipo_descargos,tipoDescargo,'.$tipoDescargo->id.'|min:2|max:200',
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'tipoDescargo' => $validacion->errors()->get('tipoDescargo')
            ]);
        }

        $tipoDescargo->tipoDescargo = $request->input('tipoDescargo');
        $tipoDescargo->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'El tipo de descargo fue actualizado con exito',
            'tipoDescargo' => [ 'id' => $tipoDescargo->id, 'tipoDescargo' => $tipoDescargo->tipoDescargo, 'eliminado' => 0 ]
        ]);
    }

    function delete ( TipoDescargos $tipoDescargo , $accion )
    {
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {
            $contador = $tipoDescargo->descargos->count();
        }

        if ( $contador == 0)
        {
            $tipoDescargo->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
            $tipoDescargo->save();

            $mensaje = $accion ? 'Tipo de descargo '. $tipoDescargo->tipoDescargo.' ha sido desactivado con exito' : 'Tipo de descargo '. $tipoDescargo->tipoDescargo.' ha sido restaurado con exito';
            
            return response()->json([
                'respuesta' => true,
                'mensaje' => $mensaje
            ]);
        } else
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar el tipo de descargo, debido que hay registros que hacen referencia a el'
            ]);
        }
    }
}

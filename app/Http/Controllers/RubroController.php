<?php

namespace App\Http\Controllers;
use App\Models\Rubro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class RubroController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index ()
    {
        return view('rubros.index');
    }

    function getRubros ()
    {
        return response()->json([
            'rubros' => Rubro::all()
        ]);
    }

    function save ( Request $request )
    {
        if ($request->isMethod('POST')) {
            
            $validacion = Validator::make($request->all(), [
                'rubro' => 'required|unique:rubros|min:2|max:100'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'rubro' => $validacion->errors()->get('rubro')
                ]);
            }

            $rubro = new Rubro();
            $rubro->rubro = $request->get('rubro');
            $rubro->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El rubro ha sido agregado exitosamente',
                'rubro' => [ 'id' => $rubro->id, 'rubro' => $rubro->rubro, 'eliminado' => 0 ]
            ]);
        }
    }

    function update (Request $request, Rubro $rubro ) 
    {
        $validacion = Validator::make($request->all(), [
            'rubro' => 'required|unique:rubros|min:2|max:100'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => $validacion->errors()->get('rubro')
            ]);
        } else 
        {
            $rubro->rubro = $request->input('rubro');
            $rubro->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El rubro ha sido actualizado con exito'
            ]);
        }

    }

    function delete ( Rubro $rubro, $accion )
    {
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {
            $contador = $rubro->inventario->count();
        }

        if ( $contador == 0)
        {
            $rubro->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
            $rubro->save();

            $mensaje = $accion ? 'El rubro '. $rubro->rubro.' ha sido desactivado con exito' : 'El rubro '. $rubro->rubro.' ha sido restaurado con exito';
            return response()->json([
                'respuesta' => true,
                'mensaje' => $mensaje
            ]);
        } else 
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar el rubro, debido que hay registros que hacen referencia a ella'
            ]);
        }
        

    }
}

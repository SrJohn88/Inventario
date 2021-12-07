<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarcaController extends Controller
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

    public function index()
    {
        return view('admin.marcas');
    }

    function obtenerMarcas()
    {
        return response()->json([
            'marcas' => Marca::all()
        ]);
    }

    function save( Request $request ) 
    {
        if ($request->isMethod('POST')) 
        {
            $validacion = Validator::make($request->all(), [
                'marca' => 'required|unique:marcas|min:2|max:100'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'marca' => $validacion->errors()->get('marca')
                ]);
            }

            $marca = new Marca();
            $marca->marca = $request->input('marca');
            $marca->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'La marca ha sido agregado exitosamente',
                'marca' => ['id' => $marca->id , 'marca' => $marca->marca, 'eliminado' => 0 ]
            ]);
        }
    }

    function update( Request $request, Marca $marca) 
    {
        $validacion = Validator::make($request->all(), [
            'marca' => 'required|unique:marcas,marca,'.$marca->id.'|min:2|max:100'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'marca' => $validacion->errors()->get('marca')
            ]);
        }

        $marca->marca = $request->input('marca');
        $marca->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'La marca ha sido actualizada con exito',
            'marca' => ['id' => $marca->id , 'marca' => $marca->marca, 'eliminado' => 0 ]
        ]);

    }

    function delete( Marca $marca, $accion )
    {
        $contador = 0;

        if ( filter_var($accion, FILTER_VALIDATE_BOOLEAN) )
        {
            $contador = $marca->inventario->count();
        }

        if ( $contador == 0)
        {
            $marca->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
            $marca->save();
            
            $mensaje = filter_var($accion, FILTER_VALIDATE_BOOLEAN) 
            ? 'La marca ha sido desactivada con exito' 
            : 'La marca ha sido activada con exito';

            return response()->json([
            'respuesta' => true,
            'mensaje' => $mensaje
            ]);
        } else 
        {
            return response()->json([
                'respuesta' => false,
                'mensaje' => 'No se puede desactivar la marca, debido que hay registros que hacen referencia a ella'
            ]);
        }
    }

}

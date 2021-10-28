<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Inventario;

class InventarioController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index() 
    {
        return view('Inventario.index');
    }

    function crear()
    {
        return view('Inventario.crear');
    }

    function getActivos() 
    {
        return response()->json([
            'activos' => Inventario::with('ubicacion', 'estado', 'marca', 'estado')->get()
        ]);
    }

    function getOneActivo ( $id )
    {        
        return response()->json([
            'activo' => Inventario::with('ubicacion', 'marca', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')->where('id', $id)->firstOrFail()
        ]);
    }

    function save( Request $request ) {

        if( $request->isMethod('POST') )
        {   
            $validacion = Validator::make($request->all(), [
                'codigo' => 'required|unique:inventarios|min:2|max:50'
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'inventario' => $validacion->errors()->get('codigo')
                ]);
            }

            $inventario = new Inventario();
            $inventario->codigo = $request->get('codigo');
            $inventario->serie = $request->get('serie');
            $inventario->descripcion = $request->get('descripcion');
            $inventario->marca_id = $request->input('marca.id');
            $inventario->modelo = $request->get('modelo');
            $inventario->procedencia_id = $request->input('procedencia.id');
            $inventario->cuenta_id = $request->input('cuenta.id');
            $inventario->entidad_id = $request->input('entidad.id');
            $inventario->precio = $request->get('precio');
            $inventario->rubro_id = $request->input('rubro.id');
            $inventario->ubicacion_id = $request->input('ubicacion.id');
            $inventario->fecha_adquision = $request->get('fecha');
            $inventario->observacion = $request->get('observaciones');
            $inventario->estado_id = 1;
            $inventario->save();

            //return $request->all();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El producto a sido registrado con exito',
                'procedencia' => $inventario
            ]);
        }

    }

    function update( Request $request, Inventario $inventario ) {

        // return $inventario;

        $validacion = Validator::make($request->all(), [
            'codigo' => 'required|unique:inventarios,codigo,'.$inventario->id.'|min:2|max:100'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'inventario' => $validacion->errors()->get('codigo')
            ]);
        }

        $inventario->codigo = $request->get('codigo');
        $inventario->serie = $request->get('serie');
        $inventario->descripcion = $request->get('descripcion');
        $inventario->marca_id = $request->input('marca.id');
        $inventario->modelo = $request->get('modelo');
        $inventario->procedencia_id = $request->input('procedencia.id');
        $inventario->cuenta_id = $request->input('cuenta.id');
        $inventario->entidad_id = $request->input('entidad.id');
        $inventario->precio = $request->get('precio');
        $inventario->rubro_id = $request->input('rubro.id');
        $inventario->ubicacion_id = $request->input('ubicacion.id');
        $inventario->fecha_adquision = $request->get('fecha');
        $inventario->observacion = $request->get('observaciones');
        $inventario->save();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El producto a sido actualizado con exito',
                'procedencia' => $inventario
            ]);

    }
}

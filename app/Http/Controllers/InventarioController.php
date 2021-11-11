<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Inventario;
use App\Models\HistorialInventario;

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

    function detalle()
    {
        return view('Inventario.detalle');
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
            'activo' => Inventario::with('ubicacion', 'marca', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado', 'historial')->where('id', $id)->firstOrFail()
        ]);
    }

    function save( Request $request ) {

        if( $request->isMethod('POST') )
        {   
            $validacion = Validator::make($request->all(), [
                'codigo' => 'required|unique:inventarios|min:2|max:50',
                'descripcion' => 'required|min:2',
                'procedencia.id' => 'required',
                'rubro' => 'required',
                'fecha' => 'required'
                ,'ubicacion.id' => 'required',
                'rubro.id' => 'required'  
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'errors' => $validacion->errors()
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

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'El producto a sido registrado con exito',
                'inventario' => $inventario
            ]);
        }

    }

    function update( Request $request, Inventario $inventario ) {

        $validacion = Validator::make($request->all(), [
            'codigo' => 'required|unique:inventarios,codigo,'.$inventario->id.'|min:2|max:100',
            'descripcion' => 'required|min:2',
            'procedencia.id' => 'required',
            'rubro' => 'required',
            'fecha' => 'required',
            'ubicacion.id' => 'required',
           'rubro.id' => 'required' 
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'errors' => $validacion->errors()->get('codigo')
            ]);
        }

        $guardarCopia = false;
        $guardarCopia = filter_var( $request->input('guardarHistorial'), FILTER_VALIDATE_BOOLEAN);;


        if ( $guardarCopia )
        {
            $historialInventario = new HistorialInventario();
            $historialInventario->inventario_id = $inventario->id;
            $historialInventario->codigo = $inventario->codigo;
            $historialInventario->serie = $inventario->serie;
            $historialInventario->descripcion = $inventario->descripcion;
            $historialInventario->marca_id = $inventario->marca_id;
            $historialInventario->modelo = $inventario->modelo;
            $historialInventario->procedencia_id = $inventario->procedencia_id;
            $historialInventario->entidad_id = $inventario->entidad_id;
            $historialInventario->cuenta_id = $inventario->cuenta_id;
            $historialInventario->precio = $inventario->precio;
            $historialInventario->rubro_id = $inventario->rubro_id;
            $historialInventario->ubicacion_id = $inventario->ubicacion_id;
            $historialInventario->desUbicacion = $inventario->desUbicacion;
            $historialInventario->fecha_adquision = $inventario->fecha_adquision;
            $historialInventario->observacion = $inventario->observacion;
            $historialInventario->save();
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

    function obtenerHistorialMovimientos()
    {
        return Inventario::with('historial')->get();
    }
}

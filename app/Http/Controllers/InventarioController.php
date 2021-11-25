<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Inventario;
use App\Models\HistorialInventario;
use App\Models\TipoDescargos;
Use \App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;

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
            'activos' => Inventario::with('ubicacion', 'estado', 'marca', 'estado')
                        ->where('eliminado', false)
                        ->get()
        ]);
    }

    function getOneActivo ( $id )
    {   
        return response()->json([
            'activo' => Inventario::with('ubicacion', 'marca', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado', 'historial')
            ->where('id', $id)
            ->firstOrFail()
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
            $inventario->precio = $request->get('precio') ?  sprintf("%.2f", $request->get('precio')) : null ;
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
            $historialInventario->user_id = \Auth::user()->id;
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
        $inventario->precio = $inventario->precio = $request->get('precio') ?  sprintf("%.2f", $request->get('precio')) : null ;
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

    function indexActivosDescargados()
    {
        return view('Inventario.descargos.activosDescargados');
    }

    function obtenerActivosDescargados()
    {
        return response()->json([
            'activos' => Inventario::with('ubicacion', 'estado', 'marca', 'estado', 'descargo')
                        ->where('eliminado', true )
                        ->get()
        ]);
    }


    // REPORTES

    function reporteProcedencia()
    {
        return view('Reportes.procedencia');
    }

    function reporteGeneral()
    {
        return view('Reportes.inventarioG');
    }

    function reporteDescargos ()
    {
        return view('Reportes.descargos');
    }

    function reporteCompras ()
    {
        return view('Reportes.compras');
    }

    function reporteMovimientos ()
    {
        return view('Reportes.movimientos');
    }

    function inventaroPorUbicacion( $desde, $hasta, Ubicacion $ubicacion)
    {
        $inventario = [];

        if ( $ubicacion->id ) 
        {
            $inventario = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                            ->whereDate('fecha_adquision', '>=', $desde )
                            ->whereDate('fecha_adquision', '<=', $hasta)
                            ->orderBy('fecha_adquision', 'desc' )
                            ->where('ubicacion_id', $ubicacion->id )
                            ->where('eliminado', false)
                            ->get();
        } else 
        {
            $inventario = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                            ->whereDate('fecha_adquision', '>=', $desde )
                            ->whereDate('fecha_adquision', '<=', $hasta)
                            ->where('eliminado', false)
                            ->orderBy('fecha_adquision', 'desc')
                            ->get();
        }

        return response()->json([ 'activos' => $inventario ]);
    }

    function inventarioPorEntidadRubro( Request $request )
    {          
        $activos = [];        

        if ( $request->input('entidad.id') && $request->input('rubro.id') )
        {
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                                    ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                                    ->whereDate('fecha_adquision', '<=', $request->input('hasta'))
                                    ->where('procedencia_id', $request->input('procedencia.id'))
                                    ->where('entidad_id', $request->input('entidad.id'))
                                    ->where('rubro_id', $request->input('rubro.id'))
                                    ->where('eliminado', false)
                                    ->orderBy('fecha_adquision', 'desc')
                                    ->get();
            
        } else if (  $request->input('entidad.id') && !$request->input('rubro.id')  )
        {        
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                                    ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                                    ->whereDate('fecha_adquision', '<=', $request->input('hasta'))
                                    ->where('procedencia_id', $request->input('procedencia.id'))
                                    ->where('entidad_id', $request->input('entidad.id'))                                    
                                    ->where('eliminado', false)
                                    ->orderBy('fecha_adquision', 'desc')
                                    ->get();

        } else if (  !$request->input('entidad.id') && $request->input('rubro.id')  )
        {
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                                    ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                                    ->whereDate('fecha_adquision', '<=', $request->input('hasta'))                                    
                                    ->where('procedencia_id', $request->input('procedencia.id'))
                                    ->where('rubro_id', $request->input('rubro.id'))
                                    ->where('eliminado', false)
                                    ->orderBy('fecha_adquision', 'desc')
                                    ->get();           
        } else if (  !$request->input('entidad.id') && !$request->input('rubro.id')  )
        {
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                                    ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                                    ->whereDate('fecha_adquision', '<=', $request->input('hasta'))                                    
                                    ->where('procedencia_id', $request->input('procedencia.id'))                                    
                                    ->where('eliminado', false)
                                    ->orderBy('fecha_adquision', 'desc')
                                    ->get();           
        }  
        
        return response()->json([
            'respuesta' => true,
            'activos' => $activos               
        ]);
    }

    function inventarioPorCompras( Request $request )
    {
        $activos = [];

        if ( $request->input('cuenta.id') && $request->input('rubro.id') )
        {
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                        ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                        ->whereDate('fecha_adquision', '<=', $request->input('hasta'))
                        ->where('procedencia_id', 1)
                        ->where('cuenta_id', $request->input('cuenta.id'))
                        ->where('rubro_id', $request->input('rubro.id'))
                        ->where('eliminado', false)
                        ->orderBy('fecha_adquision', 'desc')
                        ->get();

        } else if ( $request->input('cuenta.id') && !$request->input('rubro.id') )
        {
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                        ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                        ->whereDate('fecha_adquision', '<=', $request->input('hasta'))
                        ->where('procedencia_id', 1)
                        ->where('cuenta_id', $request->input('cuenta.id'))                        
                        ->where('eliminado', false)
                        ->orderBy('fecha_adquision', 'desc')
                        ->get();

        } else if ( !$request->input('cuenta.id') && $request->input('rubro.id'))
        {
            $activos = Inventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'estado')
                        ->whereDate('fecha_adquision', '>=', $request->input('desde'))
                        ->whereDate('fecha_adquision', '<=', $request->input('hasta'))
                        ->where('procedencia_id', 1)                        
                        ->where('rubro_id', $request->input('rubro.id'))
                        ->where('eliminado', false)
                        ->orderBy('fecha_adquision', 'desc')
                        ->get();
        }

        return response()->json([            
            'activos' => $activos               
        ]);
    }

    function ReporteActivosDescargados( TipoDescargos $tipoDescargo, $desde, $hasta)
    {
        $descargoTemp = [];
        
        $descargos = \App\Models\Descargo::with('inventario', 'inventario.marca', 'inventario.entidad', 'inventario.cuenta', 'inventario.ubicacion', 'inventario.procedencia', 'inventario.rubro')
                                            ->where('tipoDescargo_id', $tipoDescargo->id)
                                            ->whereDate('fechaActa', '>=', $desde )
                                            ->whereDate('fechaActa', '<=', $hasta )
                                            ->get();

        foreach ( $descargos as $descargo )
        {
            foreach( $descargo->inventario as $activo )
            {
                $activo->FechaActa = $descargo->fechaActa;
                $activo->acta = $descargo->acta;
                array_push($descargoTemp, $activo );
            }            
        }

        return response()->json([
            'activos' => $descargoTemp            
        ]);
    }

    function ReporteInventarioMovimientos(\App\Models\TipoMovimiento $tipoMovimiento, $desde, $hasta, $accion )
    {
        $activos = [];

        $movimientos = \App\Models\Movimiento::with('tipoMovimiento', 'inventario', 'inventario.marca', 'inventario.entidad', 'inventario.cuenta', 'inventario.ubicacion', 'inventario.procedencia', 'inventario.rubro')
                        ->where('tipo_id', $tipoMovimiento->id)
                        ->whereDate('created_at', '>=', $desde )
                        ->whereDate('created_at', '<=', $hasta )
                        ->get();

        foreach( $movimientos as $movimiento )
        {
             foreach( $movimiento->inventario as $inventario )
             {
                $inventario->idMovimiento = $movimiento->id;
                $inventario->tipoMovimiento = $movimiento->tipoMovimiento->tipo;
                $inventario->fechaCreacion = $movimiento->created_At;

                if ( $accion == 'COMPLETOS' )
                {
                    if ( $inventario->pivot->recibido)
                    {
                        array_push( $activos, $inventario );
                    } 
                } else 
                {
                    if ( !$inventario->pivot->recibido)
                    {
                        array_push( $activos, $inventario );
                    } 
                }
                
             }
        }

        return response()->json([ 'activos' => $activos ]);
    }
}

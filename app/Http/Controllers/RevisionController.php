<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RevisionController extends Controller
{
    private $mensajes = [
        'required' => 'Este campo es requerido',
        'min' => 'El campo debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

    function index()
    {
        return view('Revision.index');

    }

    function formulario()
    {
        return view('Revision.crear');
    }

    function obtenerRevisiones()
    {
        return response()->json([
            'revisiones' => Revision::with('user', 'inventario')->get()
        ]);        
    }

    function obtenerRevision( Revision $revision )
    {
        return response()->json([
            'revisiones' => Revision::with('user', 'inventario')->where('id', $revision->id)->get()
        ]); 
    }

    function save( Request $request )
    {
        try {

            DB::beginTransaction();

            $validacion = Validator::make($request->all(), [
                'nombre' => 'required|min:2|max:200',                
            ], $this->mensajes);

            if ($validacion->fails()) {
                return response()->json([
                    'respuesta' => false,
                    'mensaje' => '',
                    'errors' => $validacion->errors()
                ]);
            }

            $inventario = \App\Models\Inventario::where('eliminado', false)->get();
            
            $revision = new Revision();
            $revision->nombre = $request->input('nombre');
            $revision->user_id = \Auth::user()->id;
            $revision->save();

            foreach ( $inventario as $inventario )
            {
                $revision->inventario()->attach( $inventario->id, [
                    'revisado' => false,
                    'esCorrecto' => null,
                    'observacion' => null
                ]);
            }
        
            DB::commit();

            return response()->json([
                'respuesta' => true,
                'mensaje' => 'Revisión de inventario creada con exito'
            ]);

        } catch( \Exception $e  )
        {
            DB::rollBack();
            return response()->json([
                'respuesta' => false,
                'error' => $e->getMessage(),
                'mensaje' => 'Ocurrio un error en el servidor'
            ]);
        }
    }
}

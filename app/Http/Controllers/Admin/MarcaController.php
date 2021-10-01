<?php

namespace App\Http\Controllers\Admin;

use App\Models\Marca;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{

    public function index() {
        return view('admin.marcas');
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{

            DB::beginTransaction();
            //metodo para guardar este es el modelo
            $marca = new Marca();
            $marca->nombre = $request->nombre;
            $marca->estado = "A";
            if (Marca::where('nombre', $marca->nombre)->doesntExist()) {
                # code...
                $marca->save();
            } else {
                # code...
                return response()->json(array(
                    'message' => 'Esta Marca Ya Existe'
                ), 400);
            }
            
            DB::commit();
        }catch(\Exception $e){
            DB::roollBack();
            return $e->getMessage();
        }
    
        /* $ubicacion = new Ubicacion();
        $ubicacion = Ubicacion::findOrFail($request->id);
        $ubicacion->nombre = $request->nombre;
        $ubicacion->estado = "A";
        $ubicacion->save();
        if (Ubicacion::where('nombre', $ubicacion->nombre)->doesntExist()) {
            # code...
            $ubicacion->save();
        } else {
            # code...
            return response()->json(array(
                'message' => 'Esta Ubicacion Ya Existe'
            ), 400);
        } */
    }

    public function update(Request $request)
    {   $marca = new Marca();
        $marca = Marca::findOrFail($request->id);
        $marca->nombre = $request->nombre;
      
        if (Marca::where('nombre', $marca->nombre)->doesntExist()) {
            # code...
            $marca->save();
        } else {
            # code...
            return response()->json(array(
                'message' => 'Esta Categoria Ya Existe'
            ), 400);
        }
    }
    public function change(Request $request) {
        try{
            $marca = Marca::findOrFail($request->id);
            $marca->estado = $request->estado;
            if($request->estado=='R'){
            }
            $marca->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
    
    public function getMarca(Request $request){
        $cond = $request->type;
        if($cond=='A'){
            $marcas = Marca::where('marcas.estado','=','A')
                ->orderBy('marcas.id','desc')->get();
        }else{
            $marcas = Marca::where('marcas.estado','=','R')
                ->orderBy('marcas.id','desc')->get();
        }
        return $marcas;
    }
}

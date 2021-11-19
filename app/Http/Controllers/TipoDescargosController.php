<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDescargos;

class TipoDescargosController extends Controller
{
    function obtenerTiposDescargos()
    {
        return response()->json([
            'tiposDescargos' => TipoDescargos::where('eliminado', false )->get()
        ]);
    }
}

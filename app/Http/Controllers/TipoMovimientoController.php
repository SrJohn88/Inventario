<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoMovimiento;


class TipoMovimientoController extends Controller
{
    function obtenerTiposMovimientos() 
    {
        return response()->json([
            'tiposMovimientos' => TipoMovimiento::all()
        ]);
    }
}

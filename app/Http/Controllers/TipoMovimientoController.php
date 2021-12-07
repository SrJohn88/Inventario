<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoMovimiento;


class TipoMovimientoController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    
    function obtenerTiposMovimientos() 
    {
        return response()->json([
            'tiposMovimientos' => TipoMovimiento::all()
        ]);
    }
}

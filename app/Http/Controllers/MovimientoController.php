<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    function crear() 
    {
        return view('Inventario.movimientos.crear');
    }
}

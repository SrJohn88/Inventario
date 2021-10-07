<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventarioController extends Controller
{
    function crear()
    {
        return view('Inventario.crear');
    }
}

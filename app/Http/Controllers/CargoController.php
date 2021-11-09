<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;

class CargoController extends Controller
{
    function obtenerCargos() 
    {
        return response()->json([
            'cargos' => Cargo::all()
        ]);
    }
}

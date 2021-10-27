<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;

class CargoController extends Controller
{
    function obtenerEmpleado() 
    {
        return response()->json([
            'empleados' => Empleado::all()
        ]);
    }
}

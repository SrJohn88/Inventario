<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    function obtenerEmpleados() 
    {
        return response()->json([
            'empleados' => Empleado::all()
        ]);
    }
}

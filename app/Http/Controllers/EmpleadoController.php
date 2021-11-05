<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    function index()
    {
        return view('Empleados.index');
    }

    function obtenerEmpleados() 
    {
        return response()->json([
            'empleados' => Empleado::all()
        ]);
    }
}

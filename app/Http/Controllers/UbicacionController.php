<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ubicacion;

class UbicacionController extends Controller
{
    function index()
    {
        return view('Ubicaciones.index');
    }

    function getUbicaciones()
    {
        return response()->json([
            'ubicaciones' => Ubicacion::all() 
        ]);
    }

    function save()
    {

    }

    function update()
    {

    }

    function delete()
    {

    }
}

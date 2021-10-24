<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Procedencia;

class ProcedenciaController extends Controller
{
    function getProcedencias() {
        return response()->json([
            'procedencias' => Procedencia::all()
        ]);
    }
}

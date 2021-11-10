<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialInventario;
use App\Models\Inventario;

class HistorialInventarioController extends Controller
{
    function obtenerHistorialActivo ( Inventario $inventario, $desde, $hasta)
    {
        return response()->json([
            'historial' => HistorialInventario::where('inventario_id', $inventario->id )
                                            ->whereBetween('created_at', [$desde, $hasta ] )->get()
        ]);
    }
}

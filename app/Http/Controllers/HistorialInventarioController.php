<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialInventario;
use App\Models\Inventario;

class HistorialInventarioController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    
    function obtenerHistorialActivo ( Inventario $inventario, $desde, $hasta)
    {
        return response()->json([
            'historial' => HistorialInventario::with('marca', 'ubicacion', 'cuenta', 'procedencia', 'rubro', 'entidad', 'user')
                                            ->where('inventario_id', $inventario->id )
                                            ->whereDate('created_at', '>=', $desde )
                                            ->whereDate('created_at', '<=', $hasta )
                                            ->orderBy('created_at', 'desc')
                                            ->get()
        ]);
    }
}

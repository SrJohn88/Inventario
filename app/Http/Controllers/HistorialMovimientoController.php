<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\HistorialMovimiento;

class HistorialMovimientoController extends Controller
{
    function obtenerHistorialMovimientos ( Inventario $inventario, $desde, $hasta )
    {
        return response()->json([
            'historial' => HistorialMovimiento::where('inventario_id', '=', $inventario->id )
                                            ->whereDate('created_at', '>=', $desde )
                                            ->whereDate('created_at', '<=', $hasta )
                                            ->orderBy('created_at', 'desc')
                                            ->get()
        ]);
    }
}

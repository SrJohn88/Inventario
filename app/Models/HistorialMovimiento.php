<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HistorialMovimiento extends Pivot
{
    use HasFactory;

    protected $table = 'historial_movimientos';

    function inventario()
    {
        return $this->belongsTo('App\Models\Inventario');
    }

    function movimiento()
    {
        return $this->belongsTo('App\Models\Movimiento');
    }

    public function getCreatedAtAttribute( $date )
    {        
        return Carbon::createFromDate( $date )->format('d-m-Y H:m A') ;
    }

    public function getUpdatedAtAttribute( $date )
    {        
        return Carbon::createFromDate( $date )->format('d-m-Y H:m A') ;
    }
}

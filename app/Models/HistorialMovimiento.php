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
        return Carbon::createFromDate($date)
            ->setTimezone('America/El_Salvador')
            ->format('d-m-Y h:i:s A');                
    }

    public function getUpdatedAtAttribute( $date )
    {        
        return Carbon::createFromDate($date)
            ->setTimezone('America/El_Salvador')
            ->format('d-m-Y h:i:s A');
    }
}

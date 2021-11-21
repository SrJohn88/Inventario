<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    use HasFactory;

    protected $table = 'historial_inventarios';


    function inventario()
    {
        return $this->belongsTo('App\Models\Inventario');
    }

    function marca()
    {
        return $this->belongsTo('App\Models\Marca');
    }

    function ubicacion()
    {
        return $this->belongsTo('App\Models\Ubicacion');
    }

    function cuenta()
    {
        return $this->belongsTo('App\Models\Cuenta');
    }

    function rubro()
    {
        return $this->belongsTo('App\Models\Rubro');
    }

    function procedencia()
    {
        return $this->belongsTo('App\Models\Procedencia');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    function entidad()
    {
        return $this->belongsTo('App\Models\Entidad');
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

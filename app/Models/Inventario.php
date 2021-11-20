<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

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

    function entidad()
    {
        return $this->belongsTo('App\Models\Entidad');
    }

    function estado()
    {
        return $this->belongsTo('App\Models\Estado');
    }

    function movimiento() 
    {
        return $this->belongsToMany('App\Models\Movimiento')->withTimestamps()->withPivot('falla', 'observaciones', 'recibido', 'usuario');
    }

    public function historial(){
        return $this->hasMany('App\Models\HistorialMovimiento');
    }

    public function respaldos (){
        return $this->hasMany('App\Models\HistorialInventario');
    }

    function descargo()
    {
        return $this->belongsToMany('App\Models\Descargo')->withTimestamps()->withPivot('observacion');
    }
}

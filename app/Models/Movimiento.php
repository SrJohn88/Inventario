<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class Movimiento extends Model
{
    use HasFactory;

    function inventario()
    {
        return $this->belongsToMany('App\Models\Inventario')->withTimestamps()->withPivot('falla', 'observaciones', 'recibido', 'usuario');
    }

    function tipoMovimiento()
    {
        return $this->belongsTo('App\Models\TipoMovimiento', 'tipo_id');
    }

    function ubicacion()
    {
        return $this->belongsTo('App\Models\Ubicacion', 'seTranslada');
    }

    function recibe() 
    {
        return $this->belongsTo('App\Models\Empleado', 'recibido_por');
    }

    function aprueba() 
    {
        return $this->belongsTo('App\Models\Empleado', 'aprobado_por');
    }

    function aprobadoGerencia() 
    {
        return $this->belongsTo('App\Models\Empleado', 'aprobado_gerencia');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
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

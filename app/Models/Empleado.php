<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';

    function cargo ()
    {
        return $this->belongsTo('App\Models\Cargo');
    }
    
    public function recibidoPor()
    {
        return $this->hasMany('App\Models\Movimiento', 'recibido_por');
    }

    public function aprobadoPor()
    {
        return $this->hasMany('App\Models\Movimiento', 'aprobado_por');
    }

    public function aprobadoGerencia()
    {
        return $this->hasMany('App\Models\Movimiento', 'aprobado_gerencia');
    }

    public function seTraslada()
    {
        return $this->hasMany('App\Models\Empleado', 'seTranslada');
    }
}

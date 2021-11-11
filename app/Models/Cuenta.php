<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuentas';
    
    protected $fillable = ['cuenta'];

    function inventario()
    {
        return $this->hasMany('App\Models\Inventario');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    function inventario()
    {
        return $this->belongsToMany('App\Models\Inventario');
    }
}

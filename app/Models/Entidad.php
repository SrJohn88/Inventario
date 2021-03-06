<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = 'entidades';
    
    protected $fillable = ['entidad'];

    function inventario()
    {
        return $this->hasMany('App\Models\Inventario');
    }
}

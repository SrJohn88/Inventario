<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{

    protected $table = 'rubros';
    
    protected $fillable = ['rubro'];

    function inventario()
    {
        return $this->hasMany('App\Models\Inventario');
    }
    
}

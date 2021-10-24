<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';
    
    protected $fillable = ['nombre'];

    
    public function inventario(){
        return $this->hasMany('App\Models\Marca');
    }
}

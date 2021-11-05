<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';
    
    protected $fillable = ['marca'];

    
    public function inventario(){
        return $this->hasMany('App\Models\Marca');
    }
}

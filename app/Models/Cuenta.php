<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $table = 'cuentas';
    
    protected $fillable = ['cuenta'];

    public function rubros(){
        
        return $this->hasMany('App\Models\Rubro');
    }
}

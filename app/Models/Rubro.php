<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{

    protected $table = 'rubros';
    
    protected $fillable = ['rubro'];

    public function cuenta ()
    {
        return $this->belongsTo('App\Models\Cuenta');
    }

}

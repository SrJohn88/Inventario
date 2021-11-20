<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDescargos extends Model
{
    use HasFactory;

    protected $table = 'tipo_descargos';

    public function descargos ( ) 
    {
        return $this->hasMany('App\Models\Descargo', 'tipoDescargo_id');
    }
}

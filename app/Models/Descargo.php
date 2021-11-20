<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descargo extends Model
{
    use HasFactory;

    protected $table = 'descargos';

    function tipoDescargo ()
    {
        return $this->belongsTo('App\Models\TipoDescargo');
    }
}

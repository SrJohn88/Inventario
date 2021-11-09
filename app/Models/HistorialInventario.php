<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    use HasFactory;

    protected $table = 'historial_inventarios';


    function inventario()
    {
        return $this->belongsTo('App\Models\Inventario');
    }

    public function getCreatedAtAttribute( $date )
    {        
        return Carbon::createFromDate( $date )->format('d-m-Y H:m A') ;
    }

    public function getUpdatedAtAttribute( $date )
    {        
        return Carbon::createFromDate( $date )->format('d-m-Y H:m A') ;
    }
}

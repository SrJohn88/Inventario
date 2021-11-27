<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Revision extends Model
{
    use HasFactory;

    protected $table = 'revisiones';

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    function inventario()
    {
        return $this->belongsToMany('App\Models\Inventario')->withTimestamps()->withPivot('revisado', 'esCorrecto', 'observacion');
    }

    public function getCreatedAtAttribute( $date )
    {        
        return Carbon::createFromDate($date)
            ->setTimezone('America/El_Salvador')
            ->format('d-m-Y h:i:s A');                
    }

    public function getUpdatedAtAttribute( $date )
    {        
        return Carbon::createFromDate($date)
            ->setTimezone('America/El_Salvador')
            ->format('d-m-Y h:i:s A');
    }
}

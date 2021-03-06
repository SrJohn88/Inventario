<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    function rol ()
    {
        return $this->belongsTo('App\Models\Rol');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        return 'Administrador';
    }
    
    public function adminlte_profile_url()
    {
        return 'profile/username';
    }

    public function getCreatedAtAttribute( $date )
    {        
        return Carbon::createFromDate( $date )->format('d-m-Y') ;
    }

    public function getUpdatedAtAttribute( $date )
    {        
        return Carbon::createFromDate( $date )->format('d-m-Y') ;
    }

}

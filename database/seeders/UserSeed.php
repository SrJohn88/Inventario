<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $rol = new Rol();
        $rol->rol = 'Administrador';
        $rol->save();

        $rol1 = new Rol();
        $rol1->rol = 'Suplente';
        $rol1->save();

        $user = new User();
        $user->name = 'usuario1';
        $user->lastName = 'Apellidos 1';
        $user->email = 'roberto.guevaraactivofijo@gmail.com';
        $user->password = bcrypt('12345678');
        $user->rol_id = 1;
        $user->save();
        
    }
}

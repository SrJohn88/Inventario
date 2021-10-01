<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'usuario1';
        $user->email = 'roberto.guevaraactivofijo@gmail.com';
        $user->password = bcrypt('12345678');
        $user->save();
        
    }
}

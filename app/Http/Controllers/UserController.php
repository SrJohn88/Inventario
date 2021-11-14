<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    function index()
    {
        return view('Usuarios.index');
    }

    function obtenerUsuarios ()
    {
        return response()->json([
            'usuarios' => User::with('rol')->get()
        ]);
    }
}

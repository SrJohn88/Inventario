<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;

class UserController extends Controller
{
    private $mensajes = [
        'required' => 'El :attribute es requerido',
        'min' => 'El :attribute debe tener al menos :min caracteres.',
        'unique' => 'La :attribute ya existe',
    ];

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

    function obtenerRoles()
    {
        return response()->json([
            'roles' => Rol::all()
        ]);
    }

    function save( Request $request )
    {
        $validacion = Validator::make($request->all(), [
            'email' => 'required|unique:users|min:2|max:200',
            'nombre' => 'required',
            'apellido' => 'required',
            'rol.id' => 'required'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'errors' => $validacion->errors()
            ]);
        }

        $user = new User();
        $user->name = $request->input('nombre');
        $user->lastName = $request->input('apellido');
        $user->email = $request->input('email');
        $user->password = Hash::make( $request->input('password') );
        $user->rol_id = $request->input('rol.id');
        $user->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'El usuario ha sido agregado exitosamente'
        ]);
    }

    function update( Request $request, User $user )
    {
        $validacion = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,'.$user->id.'|min:2|max:200',
            'nombre' => 'required',
            'apellido' => 'required',
            'rol.id' => 'required'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'errors' => $validacion->errors()
            ]);
        }

        $user->name = $request->input('nombre');
        $user->apellido = $request->input('apellido');
        $user->rol_id = $request->input('rol.id');
        $user->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'El usuario ha sido actualizado exitosamente'
        ]);
    }
}

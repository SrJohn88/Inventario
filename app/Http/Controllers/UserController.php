<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    
    private $mensajes = [
        'required' => 'Campo requerido',
        'min' => 'Este campo debe tener al menos :min caracteres.',
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
            'rol.id' => 'required',
            'password' => 'required',
            'password2' => 'required'
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'errors' => $validacion->errors()
            ]);
        }

        if ( $request->input('password') == $request->input('password2') )
        {
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
        } else 
        {
            return response()->json([
                'respuesta' => false,
                'errors' => [],
                'mensaje' => 'Las contrase??as no coinciden'
            ]);
        }
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
        $user->lastName = $request->input('apellido');
        $user->rol_id = $request->input('rol.id');
        $user->save();

        return response()->json([
            'respuesta' => true,
            'mensaje' => 'El usuario ha sido actualizado exitosamente'
        ]);
    }

    function sesion() 
    {
        return response()->json([            
            'usuario' => \Auth::user(),
            'rol' => \Auth::user()->rol                             
        ]);
    }

    function crear()
    {
        return view('Usuarios.crear');
    }

    function obtenerUsuario( User $user )
    {
        return response()->json([            
            'usuario' => User::with('rol')->where('id', $user->id)->get()                       
        ]);
    }

    function cambiarContrase??a( Request $request, User $user )
    {
        $validacion = Validator::make($request->all(), [
            'actual' => 'required|min:2',
            'password' => 'required|min:2',
            'password2' => 'required|min:2',
        ], $this->mensajes);

        if ($validacion->fails()) {
            return response()->json([
                'respuesta' => false,
                'mensaje' => '',
                'errors' => $validacion->errors()
            ]);
        }
    
        if ( Hash::check( $request->input('actual'), $user->password ) )
        {
            if ( $request->input('password') == $request->input('password2') )
            {

                $user->password = Hash::make( $request->input('password') );
                $user->save();

                return response()->json([
                    'respuesta' => true,
                    'mensaje' => 'La contrase??a ha sido actualizada con exito'
                ]);
            }
            else 
            {

                return response()->json([
                    'respuesta' => false,
                    'errors' => [],
                    'mensaje' => 'Las contrase??as no coinciden'                    
                ]);
            }
        } else {
            return response()->json([
                'respuesta' => false,
                'errors' => [],
                'mensaje' => 'Upps. ??Olvidaste tu contrase??a?'
            ]);
        }

        
    }

    function resetearClave( User $user )
    {
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $passwordAleatorio = '';

        for( $i = 0; $i < 8; $i++ )
        {
            $passwordAleatorio .= substr( $cadena, rand(0,62), 1 );
        }

        $user->password = Hash::make($passwordAleatorio);
        $user->save();

        return response()->json([
            'respuesta' => true,            
            'mensaje' => 'Usuario reseteado con exito. Esta es la nueva contrase??a [ '.$passwordAleatorio.' ] para el usuario '.$user->name.' '.$user->lastName
        ]);
    }

    function desactivar( User $user, $accion )
    {
        $user->eliminado = filter_var($accion, FILTER_VALIDATE_BOOLEAN);
        $user->save();

        $mensaje = $accion ? 'El usuario '. $user->name.' '.$user->lastName.' ha sido desactivado con exito' 
                            : 'El usuario '. $user->name.' '.$user->lastName.' ha sido activado con exito' ;

        return response()->json([
                'respuesta' => true,
                'mensaje' => $mensaje
            ]);
    }

}

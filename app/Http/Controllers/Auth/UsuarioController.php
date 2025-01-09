<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class UsuarioController extends Controller
{
    public function mostrarUsuario() 
    {
        $users = User::all();
        return response()->json([
         "users" => $users
        ]);
    }

    public function actualizarUsuario(Request $request, $id)
    {
        // Primero verificamos si el usuario existe
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "Usuario no encontrado"
            ], 404);
        }
    
        // Reglas de validación
        $rules = [
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'email' => "sometimes|string|max:100|unique:users,email,$id",
            'password' => 'sometimes|string|min:8',
            'celular' => "sometimes|digits:10|regex:/^[0-9]+$/|unique:users,celular,$id",
            'esadmin' => 'sometimes|boolean',
        ];
    
        $messages = [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'celular.unique' => 'El número de celular ya está registrado.',
        ];
    
        // Validar los datos enviados
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()->all()
            ], 400);
        }
    
        // Actualizamos los datos del usuario
        $user->update([
            'nombre' => $request->get('nombre', $user->nombre),
            'apellido' => $request->get('apellido', $user->apellido),
            'email' => $request->get('email', $user->email),
            'password' => $request->has('password') ? Hash::make($request->password) : $user->password,
            'celular' => $request->get('celular', $user->celular),
            'esadmin' => $request->get('esadmin', $user->esadmin),
        ]);
    
        return response()->json([
            "status" => true,
            "message" => "Usuario actualizado con éxito",
            "data" => $user
        ], 200);
    }
    
    public function eliminarUsuario($id)
    {
        // Buscar al usuario por ID
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "Usuario no encontrado"
            ], 404);
        }
    
        // Eliminar el usuario
        $user->delete();
    
        return response()->json([
            "status" => true,
            "message" => "Usuario eliminado con éxito"
        ], 200);
    }
}

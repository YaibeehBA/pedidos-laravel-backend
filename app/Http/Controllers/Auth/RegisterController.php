<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterUserRequest;

class RegisterController extends Controller
{

    private $registerValidationRules = [
        'nombre' => 'required|string|max:150',
        'apellido' => 'required|string|max:150',
        'celular' => 'required|string|max:10|regex:/^[0-9]+$/',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed', 
        'esadmin' => 'nullable|boolean',
    ];
    private $customMessages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'apellido.required' => 'El apellido es obligatorio.',
        'celular.required' => 'El número de celular es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.unique' => 'El correo electrónico proporcionado ya está registrado. Por favor, utiliza otro correo.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function registerUser(Request $request) {
        $validateUser = Validator::make($request->all(), $this->registerValidationRules,$this->customMessages);
        
        if($validateUser->fails()){
            return response()->json([
                'message' => 'Ha ocurrido un error de validación',
                'errors' => $validateUser->errors()
            ], 422);
        }
   
        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'celular' => $request->celular,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'esadmin' => $request->esadmin ?? false, // Por defecto, 'esadmin' será falso si no se envía
        ]);

        return response()->json([
            'message' => 'El usuario se ha creado',
            'token' => $user->createToken("API ACCESS TOKEN")->plainTextToken
        ], 200);
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    private $loginValidationRules = [
        'email' => 'required|email',
        'password' => 'required'
    ];

    public function loginUser(Request $request) {
        $validateUser = Validator::make($request->all(), $this->loginValidationRules);

        if($validateUser->fails()){
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validateUser->errors()
            ], 401);
        }

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'message' => 'El email y el password no corresponden con alguno de los usuarios',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'message' => 'Login correcto',
            'token' => $user->createToken("API ACCESS TOKEN")->plainTextToken,
            'user' => $user
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'El formato del correo electrónico no es válido',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si el usuario existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No existe una cuenta registrada con este correo electrónico'
            ], 404);
        }

        // Generar token único
        $token = Str::random(64);

        // Guardar token en la tabla password_resets
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        try {
            // Enviar email
            Mail::send('emails.forgot-password', ['token' => $token], function($message) use($request) {
                $message->to($request->email);
                $message->subject('Recuperación de Contraseña');
            });

            return response()->json([
                'status' => true,
                'message' => 'Hemos enviado un enlace de recuperación a tu correo electrónico'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al enviar el correo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'status' => false,
                'message' => 'Token inválido o expirado'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontró el usuario'
            ], 404);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar token usado
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Contraseña actualizada correctamente'
        ], 200);
    }
}
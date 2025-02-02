<?php

namespace App\Http\Controllers\Notificacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        return response()->json([
            'notificaciones' => $usuario->notifications
        ]);
    }
}

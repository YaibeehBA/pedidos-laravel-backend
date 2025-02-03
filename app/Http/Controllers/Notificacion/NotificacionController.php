<?php

namespace App\Http\Controllers\Notificacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

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
  
    public function destroy()
    {
        $usuario = auth()->user();
    
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }
    
        // Eliminar todas las notificaciones del usuario
        DB::table('notifications')
            ->where('notifiable_id', $usuario->id) // Relaciona con el ID del usuario
            ->delete();
    
        return response()->json(['message' => 'Notificaciones eliminadas correctamente']);
    }

}

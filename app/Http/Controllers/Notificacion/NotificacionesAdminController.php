<?php

namespace App\Http\Controllers\Notificacion;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificacionesAdminController extends Controller
{
    public function obtenerNotificaciones()
    {
        // Obtener todas las notificaciones no leídas de tipo 'admin'
        $notificaciones = DatabaseNotification::where('read_at', null) // Solo notificaciones no leídas
            ->where('data->type', 'admin') // Filtrar por tipo 'admin' en el campo data
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones
        ]);
    }

    public function eliminarNotificacion($id)
    {
        // Buscar la notificación por ID y que sea de tipo 'admin'
        $notificacion = DatabaseNotification::where('notifiable_id', $id)
            ->where('data->type', 'admin') // Filtrar por tipo 'admin'
            ->first();

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificación no encontrada'
            ], 404);
        }

        // Eliminar la notificación
        $notificacion->delete();

        return response()->json([
            'mensaje' => 'Notificación eliminada correctamente'
        ]);
    }

    public function marcarComoLeida($id)
    {
        // Buscar la notificación por ID y que sea de tipo 'admin'
        $notificacion = DatabaseNotification::where('notifiable_id', $id)
            ->where('data->type', 'admin') // Filtrar por tipo 'admin'
            ->first();

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificación no encontrada'
            ], 404);
        }

        // Marcar la notificación como leída
        $notificacion->update(['read_at' => now()]);

        return response()->json([
            'mensaje' => 'Notificación marcada como leída'
        ]);
    }
}
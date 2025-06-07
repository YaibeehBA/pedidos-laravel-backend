<?php

namespace App\Http\Controllers\Notificacion;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificacionesAdminController extends Controller
{
    
public function obtenerNotificaciones()
    {
        try {
            
            $notificaciones = DatabaseNotification::whereNull('read_at')
                ->whereJsonContains('data', ['type' => 'admin'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($notificaciones->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No hay notificaciones pendientes',
                    'notificaciones' => [],
                    'count' => 0
                ], 200);
            }

            return response()->json([
                'success' => true,
                'notificaciones' => $notificaciones,
                'count' => $notificaciones->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener notificaciones: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar las notificaciones',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
public function eliminarNotificacion($id)
    {
        // Buscar la notificación por UUID y que sea de tipo 'admin'
        $notificacion = DatabaseNotification::where('id', $id)
            ->whereRaw("data::jsonb->>'type' = 'admin'") // Sintaxis PostgreSQL JSONB
            ->first();

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificación no encontrada o no es de tipo admin',
                'details' => 'ID proporcionado: ' . $id
            ], 404);
        }

        // Eliminar la notificación
        $notificacion->delete();

        return response()->json([
            'mensaje' => 'Notificación eliminada correctamente',
            'notification_id' => $id, // Opcional: confirmar el ID eliminado
            'deleted_at' => now()->toDateTimeString() // Opcional: timestamp de eliminación
        ]);
    }

public function marcarComoLeida($id)
    {
        // Buscar la notificación por UUID y tipo 'admin'
        $notificacion = DatabaseNotification::where('id', $id)
            ->whereRaw("data::jsonb->>'type' = 'admin'") // Sintaxis para PostgreSQL JSONB
            ->first();

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificación no encontrada o no es de tipo admin'
            ], 404);
        }

        // Marcar como leída (solo si no estaba ya marcada)
        if (is_null($notificacion->read_at)) {
            $notificacion->update(['read_at' => now()]);
            
            return response()->json([
                'mensaje' => 'Notificación marcada como leída',
                'notificacion' => $notificacion->fresh()
            ]);
        }

        return response()->json([
            'mensaje' => 'La notificación ya estaba marcada como leída',
            'notificacion' => $notificacion
        ]);
    }

}
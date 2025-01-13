<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\DetalleOrden;
use App\Models\DetalleProducto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class OrdenController extends Controller
{
    // public function crearOrden(Request $request)
    // {
    //     $validated = $request->validate([
    //         'usuario_id' => 'required|exists:users,id',
    //         'detalles' => 'required|array',
    //         'detalles.*.producto_id' => 'required|exists:productos,id',
    //         'detalles.*.cantidad' => 'required|integer|min:1',
    //     ]);

    //     $usuarioId = $validated['usuario_id'];
    //     $detalles = $validated['detalles'];

    //     // Calcular la cantidad total del pedido
    //     $cantidadTotal = array_sum(array_column($detalles, 'cantidad'));

    //     // Definir días según rangos de prendas
    //     if ($cantidadTotal <= 6) {
    //         $diasEntrega = 3;
    //     } elseif ($cantidadTotal <= 15) {
    //         $diasEntrega = 5;
    //     } elseif ($cantidadTotal <= 30) {
    //         $diasEntrega = 7;
    //     } else {
    //         return response()->json([
    //             'message' => 'El pedido supera la cantidad máxima permitida (30 prendas).',
    //         ], 400);
    //     }

    //     // Determinar la fecha inicial para el cálculo de entrega
    //     $ultimaFechaEntrega = Orden::max('fecha_entrega');
    //     if ($ultimaFechaEntrega) {
    //         $fechaInicio = Carbon::parse($ultimaFechaEntrega)->isPast() 
    //             ? now() 
    //             : Carbon::parse($ultimaFechaEntrega);
    //     } else {
    //         $fechaInicio = now(); // No hay órdenes previas
    //     }

    //     // Calcular la nueva fecha de entrega
    //     $fechaEntrega = $fechaInicio->copy()->addDays($diasEntrega);

    //     // Crear la orden
    //     $orden = Orden::create([
    //         'usuario_id' => $usuarioId,
    //         'estado' => 'pendiente',
    //         'monto_total' => 0, // Inicialmente 0
    //         'fecha_entrega' => $fechaEntrega, // Fecha calculada
    //         'estado_pago' => 'pendiente',
    //     ]);

    //     $montoTotal = 0;

    //     // Crear los detalles del pedido
    //     foreach ($detalles as $detalle) {
    //         $detalleProducto = DetalleProducto::where('producto_id', $detalle['producto_id'])->first();

    //         if (!$detalleProducto) {
    //             return response()->json([
    //                 'message' => 'Producto no encontrado.',
    //             ], 404);
    //         }

    //         $precioUnitario = $detalleProducto->precio_base;
    //         $subtotal = $precioUnitario * $detalle['cantidad'];
    //         $montoTotal += $subtotal;

    //         DetalleOrden::create([
    //             'orden_id' => $orden->id,
    //             'detalles_productos_id' => $detalleProducto->id,
    //             'cantidad' => $detalle['cantidad'],
    //             'precio_unitario' => $precioUnitario,
    //             'subtotal' => $subtotal,
    //         ]);
    //     }

    //     // Actualizar el monto total de la orden
    //     $orden->update(['monto_total' => $montoTotal]);

    //     return response()->json([
    //         'message' => 'Orden creada exitosamente',
    //         'orden' => $orden,
    //     ]);
    // }

    public function crearOrden(Request $request)
{
    $validated = $request->validate([
        'usuario_id' => 'required|exists:users,id',
        'detalles' => 'required|array',
        'detalles.*.variante_id' => 'required|exists:detalles_productos,id', // Cambiado de producto_id a variante_id
        'detalles.*.cantidad' => 'required|integer|min:1',
    ]);

    $usuarioId = $validated['usuario_id'];
    $detalles = $validated['detalles'];

    // Calcular la cantidad total del pedido
    $cantidadTotal = array_sum(array_column($detalles, 'cantidad'));

    // Definir días según rangos de prendas
    if ($cantidadTotal <= 6) {
        $diasEntrega = 3;
    } elseif ($cantidadTotal <= 15) {
        $diasEntrega = 5;
    } elseif ($cantidadTotal <= 30) {
        $diasEntrega = 7;
    } else {
        return response()->json([
            'message' => 'El pedido supera la cantidad máxima permitida (30 prendas).',
        ], 400);
    }

    // Determinar la fecha inicial para el cálculo de entrega
    $ultimaFechaEntrega = Orden::max('fecha_entrega');
    if ($ultimaFechaEntrega) {
        $fechaInicio = Carbon::parse($ultimaFechaEntrega)->isPast()
             ? now()
             : Carbon::parse($ultimaFechaEntrega);
    } else {
        $fechaInicio = now();
    }

    // Calcular la nueva fecha de entrega
    $fechaEntrega = $fechaInicio->copy()->addDays($diasEntrega);

    // Crear la orden
    $orden = Orden::create([
        'usuario_id' => $usuarioId,
        'estado' => 'pendiente',
        'monto_total' => 0,
        'fecha_entrega' => $fechaEntrega,
        'estado_pago' => 'pendiente',
    ]);

    $montoTotal = 0;

    // Crear los detalles del pedido
    foreach ($detalles as $detalle) {
        // Buscar directamente por el ID de la variante
        $detalleProducto = DetalleProducto::find($detalle['variante_id']);

        if (!$detalleProducto) {
            return response()->json([
                'message' => 'Variante de producto no encontrada.',
            ], 404);
        }

        $precioUnitario = $detalleProducto->precio_base;
        $subtotal = $precioUnitario * $detalle['cantidad'];
        $montoTotal += $subtotal;

        DetalleOrden::create([
            'orden_id' => $orden->id,
            'detalles_productos_id' => $detalleProducto->id, // Ya es el ID de la variante
            'cantidad' => $detalle['cantidad'],
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
        ]);
    }

    // Actualizar el monto total de la orden
    $orden->update(['monto_total' => $montoTotal]);

    return response()->json([
        'message' => 'Orden creada exitosamente',
        'orden' => $orden->load('detalles.detalleProducto.producto'),
    ]);
}


    public function listarOrdenes()
    {
        $ordenes = Orden::with(['detalles.detalleProducto.producto'])->get();
        return response()->json($ordenes, 200);
    }

    public function actualizarOrden(Request $request, $id)
    {
        // Validar los datos entrantes
        $validated = $request->validate([
            'estado' => 'nullable|in:pendiente,aprobada,rechazada,pagada,entregada',
            'estado_pago' => 'nullable|in:pendiente,completado',
        ]);

        // Buscar la orden por su ID
        $orden = Orden::find($id);

        // Si no encuentra la orden, devolver un error 404
        if (!$orden) {
            return response()->json([
                'message' => 'Orden no encontrada'
            ], 404);
        }

        // Actualizar los campos válidos según los datos enviados
        if ($request->has('estado')) {
            $orden->estado = $validated['estado'];
        }
        if ($request->has('estado_pago')) {
            $orden->estado_pago = $validated['estado_pago'];
        }

        // Guardar cambios en la base de datos
        $orden->save();

        // Responder con un mensaje de éxito y los detalles actualizados de la orden
        return response()->json([
            'message' => 'Orden actualizada exitosamente',
            'orden' => $orden
        ], 200);
    }
    public function eliminarOrden($id)
    {
        $orden = Orden::find($id);

        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        $orden->detalles()->delete(); // Eliminar los detalles primero
        $orden->delete(); // Luego eliminar la orden

        return response()->json(['message' => 'Orden eliminada exitosamente'], 200);
    }

    public function listarOrdenesPorUsuario($usuario_id)
    {
        // Obtener todas las órdenes del usuario
        $ordenes = Orden::where('usuario_id', $usuario_id)->get();

        // Verificar si el usuario tiene órdenes
        if ($ordenes->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron órdenes para este usuario.'
            ], 404);
        }

        return response()->json([
            'message' => 'Órdenes del usuario obtenidas exitosamente.',
            'ordenes' => $ordenes
        ], 200);
    }

    public function calcularFechaEntrega(Request $request)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1|max:30',  // Validamos la cantidad de prendas
        ], [
            'cantidad.required' => 'El campo cantidad es obligatorio.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad mínima es 1.',
            'cantidad.max' => 'La cantidad máxima es 30.',
        ]);
        
        $cantidadTotal = $validated['cantidad'];
    
        // Definir días según rangos de prendas
        if ($cantidadTotal <= 6) {
            $diasEntrega = 3;
        } elseif ($cantidadTotal <= 15) {
            $diasEntrega = 5;
        } elseif ($cantidadTotal <= 30) {
            $diasEntrega = 7;
        } else {
            return response()->json([
                'message' => 'El pedido supera la cantidad máxima permitida (30 prendas).',
            ], 400);
        }
    
        // Determinar la fecha inicial para el cálculo de entrega
        $ultimaFechaEntrega = Orden::max('fecha_entrega');
        if ($ultimaFechaEntrega) {
            $fechaInicio = Carbon::parse($ultimaFechaEntrega)->isPast() 
                ? now() 
                : Carbon::parse($ultimaFechaEntrega);
        } else {
            $fechaInicio = now(); // No hay órdenes previas
        }
    
        // Calcular la nueva fecha de entrega
        $fechaEntrega = $fechaInicio->copy()->addDays($diasEntrega);
    
        return response()->json([
            'fecha_entrega' => $fechaEntrega->toISOString(),
        ]);
    }
    

    public function obtenerOrdenesPorUsuario(Request $request, $usuarioId)
    {
        // Validar que el usuario existe
        $usuario = User::find($usuarioId);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        // Obtener todas las órdenes del usuario con sus detalles
        $ordenes = Orden::where('usuario_id', $usuarioId)
            ->with(['detallesOrden.detalleProducto.producto'])
            ->get();

        // Retornar las órdenes con su información
        return response()->json([
            'message' => 'Órdenes recuperadas exitosamente.',
            'ordenes' => $ordenes
        ]);
    }


}
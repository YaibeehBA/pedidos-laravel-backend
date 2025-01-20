<?php

namespace App\Http\Controllers\Pedidos;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Orden;
use App\Models\Talla;
use App\Models\DetalleOrden;
use Illuminate\Http\Request;
use App\Models\DetalleProducto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


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

    // public function crearOrden(Request $request)
    // {
    //     $validated = $request->validate([
    //         'usuario_id' => 'required|exists:users,id',
    //         'detalles' => 'required|array',
    //         'detalles.*.variante_id' => 'required|exists:detalles_productos,id', // Cambiado de producto_id a variante_id
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
    //         $fechaInicio = now();
    //     }

    //     // Calcular la nueva fecha de entrega
    //     $fechaEntrega = $fechaInicio->copy()->addDays($diasEntrega);

    //     // Crear la orden
    //     $orden = Orden::create([
    //         'usuario_id' => $usuarioId,
    //         'estado' => 'pendiente',
    //         'monto_total' => 0,
    //         'fecha_entrega' => $fechaEntrega,
    //         'estado_pago' => 'pendiente',
    //     ]);

    //     $montoTotal = 0;

    //     // Crear los detalles del pedido
    //     foreach ($detalles as $detalle) {
    //         // Buscar directamente por el ID de la variante
    //         $detalleProducto = DetalleProducto::find($detalle['variante_id']);

    //         if (!$detalleProducto) {
    //             return response()->json([
    //                 'message' => 'Variante de producto no encontrada.',
    //             ], 404);
    //         }

    //         $precioUnitario = $detalleProducto->precio_base;
    //         $subtotal = $precioUnitario * $detalle['cantidad'];
    //         $montoTotal += $subtotal;

    //         DetalleOrden::create([
    //             'orden_id' => $orden->id,
    //             'detalles_productos_id' => $detalleProducto->id, // Ya es el ID de la variante
    //             'cantidad' => $detalle['cantidad'],
    //             'precio_unitario' => $precioUnitario,
    //             'subtotal' => $subtotal,
    //         ]);
    //     }

    //     // Actualizar el monto total de la orden
    //     $orden->update(['monto_total' => $montoTotal]);

    //     return response()->json([
    //         'message' => 'Orden creada exitosamente',
    //         'orden' => $orden->load('detalles.detalleProducto.producto'),
    //     ]);
    // }
    // public function crearOrden(Request $request)
    // {
    //     // Validar la entrada
    //     $request->validate([
    //         'usuario_id' => 'required|exists:users,id',  // Verificar que el usuario existe
    //         'productos' => 'required|array',              // Asegurarse de que productos es un array
    //         'productos.*.detalles_productos_id' => 'required|exists:detalles_productos,id', // Productos válidos
    //         'productos.*.cantidad' => 'required|integer|min:1', // La cantidad debe ser al menos 1
    //         'productos.*.talla_id' => 'required|exists:tallas,id', // Talla válida
    //     ]);

    //     // Definir una fecha de entrega estática para pruebas
    //     $fecha_entrega = now()->addDays(3); // Por ejemplo, la fecha de entrega será dentro de 3 días

    //     // Crear la orden
    //     $orden = Orden::create([
    //         'usuario_id' => $request->usuario_id,
    //         'estado' => 'pendiente', // Orden inicialmente con estado pendiente
    //         'monto_total' => 0, // Inicialmente el monto total es 0, se actualizará después
    //         'fecha_entrega' => $fecha_entrega,
    //         'estado_pago' => 'pendiente', // Inicialmente el estado de pago es pendiente
    //     ]);

    //     // Variable para el monto total de la orden
    //     $total = 0;

    //     // Recorrer los productos del carrito de compras y crear los detalles de la orden
    //     foreach ($request->productos as $producto) {
    //         // Obtener el detalle del producto y la talla correspondiente
    //         $detalle_producto = DetalleProducto::find($producto['detalles_productos_id']);
    //         $talla = Talla::find($producto['talla_id']);

    //         if (!$detalle_producto || !$talla) {
    //             // Si no existe el producto o la talla, retornar un error
    //             return response()->json(['mensaje' => 'Producto o talla no válido'], 400);
    //         }

    //         // Calcular el subtotal para el producto
    //         $subtotal = round($detalle_producto->precio_base * $producto['cantidad'], 2);

    //         // Crear un nuevo detalle para la orden
    //         $detalleOrden = DetalleOrden::create([
    //             'orden_id' => $orden->id,
    //             'detalles_productos_id' => $producto['detalles_productos_id'],
    //             'talla_id' => $producto['talla_id'],
    //             'cantidad' => $producto['cantidad'],
    //             'precio_unitario' => $detalle_producto->precio_base,
    //             'subtotal' => $subtotal,
    //         ]);

    //         // Actualizar el monto total de la orden
    //         $total += $subtotal;
    //         $total = round($total, 2);
    //     }

    //     // Actualizar el monto total de la orden
    //     $orden->update(['monto_total' => $total]);

    //     // Responder con la orden creada
    //     return response()->json([
    //         'mensaje' => 'Orden creada exitosamente.',
    //         'orden' => $orden,
    //     ]);
    // }

    public function crearOrden(Request $request)
    {
        // Validar la entrada
        $request->validate([
            'usuario_id' => 'required|exists:users,id',  // Verificar que el usuario existe
            'productos' => 'required|array',            // Asegurarse de que productos es un array
            'productos.*.detalles_productos_id' => 'required|exists:detalles_productos,id', // Productos válidos
            'productos.*.cantidad' => 'required|integer|min:1', // La cantidad debe ser al menos 1
            'productos.*.talla_id' => 'required|exists:tallas,id', // Talla válida
        ]);

        // Calcular el total de productos en el carrito
        $totalProductos = array_reduce($request->productos, function ($carry, $producto) {
            return $carry + $producto['cantidad'];
        }, 0);

        // Definir una fecha de entrega estática para pruebas
        $fecha_entrega = now()->addDays(3); 

        // Crear la orden
        $orden = Orden::create([
            'usuario_id' => $request->usuario_id,
            'estado' => 'pendiente', // Orden inicialmente con estado pendiente
            'monto_total' => 0,      
            'fecha_entrega' => $fecha_entrega,
            'estado_pago' => 'pendiente', // Inicialmente el estado de pago es pendiente
        ]);

        // Variable para el monto total de la orden
        $total = 0;

        // Recorrer los productos del carrito de compras y crear los detalles de la orden
        foreach ($request->productos as $producto) {
            // Obtener el detalle del producto y la talla correspondiente
            $detalle_producto = DetalleProducto::find($producto['detalles_productos_id']);
            $talla = Talla::find($producto['talla_id']);

            if (!$detalle_producto || !$talla) {
                // Si no existe el producto o la talla, retornar un error
                return response()->json(['mensaje' => 'Producto o talla no válido'], 400);
            }

            // Aplicar rebaja al precio unitario si el total de productos es >= 3
            $precio_unitario = $detalle_producto->precio_base;
            if ($totalProductos >= 3) {
                $precio_unitario = max($precio_unitario - 7, 0); // Asegurarse de que no sea negativo
            }

            // Calcular el subtotal para el producto
            $subtotal = round($precio_unitario * $producto['cantidad'], 2);

            // Crear un nuevo detalle para la orden
            $detalleOrden = DetalleOrden::create([
                'orden_id' => $orden->id,
                'detalles_productos_id' => $producto['detalles_productos_id'],
                'talla_id' => $producto['talla_id'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $precio_unitario,
                'subtotal' => $subtotal,
            ]);

            // Actualizar el monto total de la orden
            $total += $subtotal;
            $total = round($total, 2);
        }

        // Actualizar el monto total de la orden
        $orden->update(['monto_total' => $total]);

        // Responder con la orden creada
        return response()->json([
            'mensaje' => 'Orden creada exitosamente.',
            'orden' => $orden,
        ]);
    }


    public function listarOrdenes(Request $request)
    
    {
        // filtrar las órdenes por estado, por ejemplo:
        $estado = $request->query('estado', null); // El parámetro 'estado' es opcional

        // Obtener las órdenes, opcionalmente filtradas por estado
        $query = Orden::with('detallesConTallasYColores'); // Carga las órdenes junto con los detalles (productos con tallas y colores)

        if ($estado) {
            // Si hay un estado proporcionado, filtramos las órdenes por ese estado
            $query->where('estado', $estado);
        }

        // Obtener las órdenes 
        $ordenes = $query->get();

        // Responder con las órdenes
        return response()->json([
            'ordenes' => $ordenes
        ]);
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

    // public function listarOrdenesPorUsuario($usuario_id)
    // {
    //     // Obtener todas las órdenes del usuario
    //     $ordenes = Orden::where('usuario_id', $usuario_id)->get();

    //     // Verificar si el usuario tiene órdenes
    //     if ($ordenes->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No se encontraron órdenes para este usuario.'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Órdenes del usuario obtenidas exitosamente.',
    //         'ordenes' => $ordenes
    //     ], 200);
    // }
    public function listarOrdenesPorUsuario($usuario_id)
    {
        // Obtener todas las órdenes del usuario con los detalles (productos, tallas, y colores)
        $ordenes = Orden::where('usuario_id', $usuario_id)
                        ->with('detallesConTallasYColores') // Cargar los detalles de productos con tallas y colores
                        ->get();
    
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

    public function listarFechas(Request $request)
{
    // Filtrar las órdenes por estado
    $estado = $request->query('estado', null); // El parámetro 'estado' es opcional

    // Consultar las órdenes incluyendo el usuario y filtrando solo los campos necesarios
    $query = Orden::query()->with(['usuario:id,nombre']); // Traer solo 'id' y 'nombre' del usuario

    if ($estado) {
        // Filtrar las órdenes por estado si está definido
        $query->where('estado', $estado);
    }

    // Seleccionar únicamente las columnas necesarias de las órdenes
    $ordenes = $query->select('id', 'fecha_entrega', 'usuario_id')->get();

    // Mapear los datos para estructurar el resultado
    $resultado = $ordenes->map(function ($orden) {
        return [
            'fecha_entrega' => $orden->fecha_entrega,
            'usuario' => $orden->usuario ? $orden->usuario->nombre : null, // Devolver 'nombre' si existe el usuario
        ];
    });

    // Responder con los datos procesados
    return response()->json([
        'ordenes' => $resultado
    ]);
}


}
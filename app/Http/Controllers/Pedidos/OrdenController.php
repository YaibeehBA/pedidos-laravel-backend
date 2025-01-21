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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

    // public function crearOrden(Request $request)
    // {
    //     // Validar la entrada
    //     $request->validate([
    //         'usuario_id' => 'required|exists:users,id',  // Verificar que el usuario existe
    //         'productos' => 'required|array',            // Asegurarse de que productos es un array
    //         'productos.*.detalles_productos_id' => 'required|exists:detalles_productos,id', // Productos válidos
    //         'productos.*.cantidad' => 'required|integer|min:1', // La cantidad debe ser al menos 1
    //         'productos.*.talla_id' => 'required|exists:tallas,id', // Talla válida
    //     ]);

    //     // Calcular el total de productos en el carrito
    //     $totalProductos = array_reduce($request->productos, function ($carry, $producto) {
    //         return $carry + $producto['cantidad'];
    //     }, 0);

    //         // Llamar al método `calcularFechaEntrega` para obtener la fecha correcta
    //         $response = Http::timeout(60)->post('http://localhost:8000/api/public/calcular-fecha-entrega', [
    //             'cantidad' => $totalProductos
    //         ]);
            
            
    //     // Definir una fecha de entrega estática para pruebas
    //     // $fecha_entrega = now()->addDays(3); 
    //      // Comprobar si la respuesta es exitosa
    //     if ($response->successful()) {
    //         // Obtener la fecha de entrega
    //         $fecha_entrega = $response->json('fecha_entrega');  
    //     } else {
    //         return response()->json(['mensaje' => 'Error al obtener la fecha de entrega.'], 400);
    //     }
    //     // Crear la orden
    //     $orden = Orden::create([
    //         'usuario_id' => $request->usuario_id,
    //         'estado' => 'pendiente', // Orden inicialmente con estado pendiente
    //         'monto_total' => 0,      
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

    //         // Aplicar rebaja al precio unitario si el total de productos es >= 3
    //         $precio_unitario = $detalle_producto->precio_base;
    //         if ($totalProductos >= 3) {
    //             $precio_unitario = max($precio_unitario - 7, 0); // Asegurarse de que no sea negativo
    //         }

    //         // Calcular el subtotal para el producto
    //         $subtotal = round($precio_unitario * $producto['cantidad'], 2);

    //         // Crear un nuevo detalle para la orden
    //         $detalleOrden = DetalleOrden::create([
    //             'orden_id' => $orden->id,
    //             'detalles_productos_id' => $producto['detalles_productos_id'],
    //             'talla_id' => $producto['talla_id'],
    //             'cantidad' => $producto['cantidad'],
    //             'precio_unitario' => $precio_unitario,
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

        $fechaEntregaResponse = $this->calcularFechaEntregaInterna($totalProductos);

        // Convertir la respuesta a un objeto PHP desde el contenido JSON
        $fechaEntregaData = json_decode($fechaEntregaResponse->getContent());
        
        // Comprobar si la respuesta indica error
        if ($fechaEntregaData->status === 'error') {
            return response()->json([
                'mensaje' => $fechaEntregaData->message
            ], 400); // Responder con un código 400 si hay un error
        }
        
        // Acceder únicamente al campo fecha_entrega
        $fecha_entrega = $fechaEntregaData->fecha_entrega;
            
    
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
    
    // public function calcularFechaEntrega(Request $request)
    // {
    //     $validated = $request->validate([
    //         'cantidad' => 'required|integer|min:1|max:30',  // Validamos la cantidad de prendas
    //     ], [
    //         'cantidad.required' => 'El campo cantidad es obligatorio.',
    //         'cantidad.integer' => 'La cantidad debe ser un número entero.',
    //         'cantidad.min' => 'La cantidad mínima es 1.',
    //         'cantidad.max' => 'La cantidad máxima es 30.',
    //     ]);
        
    //     $cantidadTotal = $validated['cantidad'];
    
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
    
    //     return response()->json([
    //         'fecha_entrega' => $fechaEntrega->toISOString(),
    //     ]);
    // }
    

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

    
    
    // public function calcularFechaEntrega(Request $request)
    // {
    //     $validated = $request->validate([
    //         'cantidad' => 'required|integer|min:1|max:30', // Validación de la cantidad
    //     ], [
    //         'cantidad.required' => 'El campo cantidad es obligatorio.',
    //         'cantidad.integer' => 'La cantidad debe ser un número entero.',
    //         'cantidad.min' => 'La cantidad mínima es 1.',
    //         'cantidad.max' => 'La cantidad máxima es 30.',
    //     ]);
    
    //     $cantidadTotal = $validated['cantidad'];
    //     $hoy = Carbon::today();
    //     $pedidosDeHoy = Orden::whereDate('created_at', $hoy)->get(); // Pedidos realizados hoy
    
    //     // Inicialización de los cupos
    //     $cupos = [
    //         '6' => $pedidosDeHoy->where('cupo', 6)->sum('cantidad'),
    //         '15' => $pedidosDeHoy->where('cupo', 15)->sum('cantidad'),
    //         '30' => $pedidosDeHoy->where('cupo', 30)->sum('cantidad'),
    //     ];
    
    //     // Verificar disponibilidad de los cupos
    //     if ($cantidadTotal <= 6 && $cupos['6'] + $cantidadTotal <= 6) {
    //         $diasEntrega = 3;
    //         $cupos['6'] += $cantidadTotal;
    //     } elseif ($cantidadTotal <= 15 && $cupos['15'] + $cantidadTotal <= 15) {
    //         $diasEntrega = 6;
    //         $cupos['15'] += $cantidadTotal;
    //     } elseif ($cantidadTotal <= 30 && $cupos['30'] + $cantidadTotal <= 30) {
    //         $diasEntrega = 12;
    //         $cupos['30'] += $cantidadTotal;
    //     } else {
    //         // Todos los cupos están llenos
    //         return response()->json([
    //             'message' => 'Lo sentimos, todos los cupos para el día de hoy están llenos. Los cupos se reinician automáticamente mañana. Intente realizar su pedido mañana.',
    //         ], 400);
    //     }
    
    //     // Calcular la fecha de entrega excluyendo fines de semana y feriados
    //     $fechaEntrega = $this->obtenerSiguienteDiaLaborable($hoy->copy()->addDays($diasEntrega));
    
    //     return response()->json([
    //         'fecha_entrega' => $fechaEntrega->toISOString(),
    //         'cupo_asignado' => ($diasEntrega == 3 ? 6 : ($diasEntrega == 6 ? 15 : 30)), // Indica a qué cupo se asignó
    //     ]);
    // }
    
    // /**
    //  * Calcular el siguiente día laborable excluyendo fines de semana y feriados
    //  */
    // private function obtenerSiguienteDiaLaborable(Carbon $fecha)
    // {
    //     $feriados = $this->obtenerFeriadosEcuador($fecha->year);
    
    //     while (in_array($fecha->format('Y-m-d'), $feriados) || $fecha->isWeekend()) {
    //         $fecha->addDay();
    //     }
    
    //     return $fecha;
    // }
    
    // /**
    //  * Obtener una lista de feriados en Ecuador para el año dado
    //  */
    // private function obtenerFeriadosEcuador($year)
    // {
    //     return [
    //         "$year-01-01", // Año Nuevo
    //         "$year-02-12", // Carnaval (día 1)
    //         "$year-02-13", // Carnaval (día 2)
    //         "$year-04-14", // Viernes Santo (ejemplo para 2024, ajusta según reglas)
    //         "$year-05-01", // Día del Trabajo
    //         "$year-08-10", // Primer Grito de Independencia
    //         "$year-11-02", // Día de los Difuntos
    //         "$year-11-03", // Independencia de Cuenca
    //         "$year-12-25", // Navidad
    //     ];
    // }

    private function calcularFeriadosEcuador(int $año): array
    {
        $feriados = [];
        
        // Feriados fijos
        $feriados = array_merge($feriados, [
            "$año-01-01", // Año Nuevo
            "$año-05-01", // Día del Trabajo
            "$año-05-24", // Batalla de Pichincha
            "$año-08-10", // Primer Grito de Independencia
            "$año-10-09", // Independencia de Guayaquil
            "$año-11-02", // Día de Difuntos
            "$año-11-03", // Independencia de Cuenca
            "$año-12-25", // Navidad
        ]);

        // Carnaval (48 y 47 días antes de Pascua)
        $pascua = Carbon::createFromFormat('Y-m-d', date('Y-m-d', easter_date($año)));
        $feriados[] = $pascua->copy()->subDays(48)->format('Y-m-d'); // Lunes Carnaval
        $feriados[] = $pascua->copy()->subDays(47)->format('Y-m-d'); // Martes Carnaval

        // Semana Santa
        $feriados[] = $pascua->copy()->subDays(2)->format('Y-m-d'); // Viernes Santo

        return $feriados;
    }

    private function getCuposDiarios()
    {
        $fechaHoy = Carbon::now()->format('Y-m-d');
        $cacheKey = "cupos_" . $fechaHoy;
        
        if (!Cache::has($cacheKey)) {
            Cache::put($cacheKey, [
                'cupo_6' => 0,
                'cupo_15' => 0,
                'cupo_30' => 0
            ], Carbon::now()->endOfDay());
        }
        
        return Cache::get($cacheKey);
    }

    private function actualizarCupos($cupos)
    {
        $fechaHoy = Carbon::now()->format('Y-m-d');
        $cacheKey = "cupos_" . $fechaHoy;
        Cache::put($cacheKey, $cupos, Carbon::now()->endOfDay());
    }

    private function calcularDiasHabiles(Carbon $fecha, int $diasAgregar)
    {
        $diasContados = 0;
        while ($diasContados < $diasAgregar) {
            $fecha->addDay();
            $añoActual = $fecha->year;
            $feriadosAñoActual = $this->calcularFeriadosEcuador($añoActual);
            
            if ($fecha->year !== $añoActual) {
                $feriadosAñoActual = array_merge(
                    $feriadosAñoActual,
                    $this->calcularFeriadosEcuador($fecha->year)
                );
            }

            if (!$fecha->isWeekend() && !in_array($fecha->format('Y-m-d'), $feriadosAñoActual)) {
                $diasContados++;
            }
        }
        return $fecha;
    }

    public function calcularFechaEntrega(Request $request)
    {
        // Validación directa del valor
        $cantidad = $request->input('cantidad');
        
        if (!is_numeric($cantidad)) {
            return response()->json([
                'status' => 'error',
                'message' => 'La cantidad debe ser un número.',
                'cantidad_solicitada' => $cantidad
            ], 400);
        }

        $cantidad = (int) $cantidad;

        if ($cantidad > 30 || $cantidad < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'La cantidad debe estar entre 1 y 30 prendas.',
                'cantidad_solicitada' => $cantidad
            ], 400);
        }

        // Obtener cupos actuales
        $cuposDiarios = $this->getCuposDiarios();

        // Verificar disponibilidad en cupos
        $fechaEntrega = null;
        $mensaje = '';

        // Intentar asignar al cupo de 6
        if ($cantidad <= 6 && ($cuposDiarios['cupo_6'] + $cantidad) <= 6) {
            $cuposDiarios['cupo_6'] += $cantidad;
            $fechaEntrega = $this->calcularDiasHabiles(Carbon::now(), 3);
            $mensaje = "Asignado al cupo de 6 prendas (3 días hábiles)";
        }
        // Intentar asignar al cupo de 15
        elseif ($cantidad <= 15 && ($cuposDiarios['cupo_15'] + $cantidad) <= 15) {
            $cuposDiarios['cupo_15'] += $cantidad;
            $fechaEntrega = $this->calcularDiasHabiles(Carbon::now(), 6);
            $mensaje = "Asignado al cupo de 15 prendas (6 días hábiles)";
        }
        // Intentar asignar al cupo de 30
        elseif ($cantidad <= 30 && ($cuposDiarios['cupo_30'] + $cantidad) <= 30) {
            $cuposDiarios['cupo_30'] += $cantidad;
            $fechaEntrega = $this->calcularDiasHabiles(Carbon::now(), 12);
            $mensaje = "Asignado al cupo de 30 prendas (12 días hábiles)";
        }

        if (!$fechaEntrega) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lo sentimos, todos los cupos del día están llenos. Por favor, intente mañana cuando se reinicien los cupos.',
                'cupos_actuales' => $cuposDiarios
            ], 400);
        }

        //    $this->actualizarCupos($cuposDiarios);

        return response()->json([
            'status' => 'success',
            'mensaje' => $mensaje,
            'fecha_entrega' => $fechaEntrega->format('Y-m-d'),
            'cantidad_prendas' => $cantidad,
            'cupos_actuales' => $cuposDiarios
        ]);
    }

    public function calcularFechaEntregaInterna($cantidad)
    {
        // Validación directa del valor
        if (!is_numeric($cantidad)) {
            return response()->json([
                'status' => 'error',
                'message' => 'La cantidad debe ser un número.',
                'cantidad_solicitada' => $cantidad
            ], 400); // Código 400 para solicitud incorrecta
        }
    
        $cantidad = (int) $cantidad;
    
        if ($cantidad > 30 || $cantidad < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'La cantidad debe estar entre 1 y 30 prendas.',
                'cantidad_solicitada' => $cantidad
            ], 400); // Código 400 para solicitud incorrecta
        }
    
        // Obtener cupos actuales
        $cuposDiarios = $this->getCuposDiarios();
    
        // Verificar disponibilidad en los cupos
        $fechaEntrega = null;
        $mensaje = '';
    
        // Intentar asignar al cupo de 6
        if ($cantidad <= 6 && ($cuposDiarios['cupo_6'] + $cantidad) <= 6) {
            $cuposDiarios['cupo_6'] += $cantidad;
            $fechaEntrega = $this->calcularDiasHabiles(Carbon::now(), 3);
            $mensaje = "Asignado al cupo de 6 prendas (3 días hábiles)";
        }
        // Intentar asignar al cupo de 15
        elseif ($cantidad <= 15 && ($cuposDiarios['cupo_15'] + $cantidad) <= 15) {
            $cuposDiarios['cupo_15'] += $cantidad;
            $fechaEntrega = $this->calcularDiasHabiles(Carbon::now(), 6);
            $mensaje = "Asignado al cupo de 15 prendas (6 días hábiles)";
        }
        // Intentar asignar al cupo de 30
        elseif ($cantidad <= 30 && ($cuposDiarios['cupo_30'] + $cantidad) <= 30) {
            $cuposDiarios['cupo_30'] += $cantidad;
            $fechaEntrega = $this->calcularDiasHabiles(Carbon::now(), 12);
            $mensaje = "Asignado al cupo de 30 prendas (12 días hábiles)";
        }
    
        if (!$fechaEntrega) {
            // Cuando no hay disponibilidad
            return response()->json([
                'status' => 'error',
                'message' => 'Lo sentimos, todos los cupos del día están llenos. Por favor, intente mañana cuando se reinicien los cupos.',
                'cupos_actuales' => $cuposDiarios
            ], 400); // Devolviendo error 400 con el mensaje adecuado
        }
    
        // Actualizamos los cupos disponibles
        $this->actualizarCupos($cuposDiarios);
    
        // Responder con éxito y los detalles de la fecha de entrega
        return response()->json([
            'status' => 'success',
            'mensaje' => $mensaje,
            'fecha_entrega' => $fechaEntrega->format('Y-m-d'),
            'cantidad_prendas' => $cantidad,
            'cupos_actuales' => $cuposDiarios
        ]);
    }
    

}

<?php

namespace App\Http\Controllers\Envios;

use App\Models\DetalleEnvio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnviosAprobadosController extends Controller
{
   
public function misEnvios()
{
    try {
        // 1. Obtener usuario autenticado
        $usuario = auth()->user();
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        // 2. Obtener TODOS los envíos del usuario con relaciones
        $envios = DetalleEnvio::with([
            'orden.usuario:id,nombre,apellido,celular,email',
            'ciudad:id,nombre,precio_envio',
            'orden.detalles' => function($query) {
                $query->with([
                    'detalleProducto.producto:id,nombre,descripcion',
                    'detalleProducto.color:id,nombre,codigo_hex', 
                    'talla:id,nombre'
                ]);
            }
        ])
        ->whereHas('orden', function($query) use ($usuario) {
            $query->where('usuario_id', $usuario->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // 3. Verificar si tiene envíos
        if ($envios->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No tienes envíos registrados',
                'data' => []
            ]);
        }

        // 4. Preparar datos del usuario una sola vez
        $usuarioData = [
            'id' => $usuario->id,
            'nombre_completo' => trim($usuario->nombre . ' ' . $usuario->apellido),
            'celular' => $usuario->celular ?? 'No especificado',
            'email' => $usuario->email,
        ];

        // 5. Formatear respuesta con cálculos correctos
        $enviosFormateados = $envios->map(function ($detalleEnvio) use ($usuarioData) {
            // Calcular valores reales
            $subtotalSinDescuento = $detalleEnvio->orden->detalles->sum(function ($detalle) {
                return ($detalle->precio_unitario + $detalle->descuento_unitario) * $detalle->cantidad;
            });
            
            $descuentoTotal = $detalleEnvio->orden->detalles->sum(function ($detalle) {
                return $detalle->descuento_unitario * $detalle->cantidad;
            });
            
            $subtotalConDescuento = $subtotalSinDescuento - $descuentoTotal;
            $total = $subtotalConDescuento + $detalleEnvio->costo_envio;

            return [
                'envio' => [
                    'id' => $detalleEnvio->id,
                    'tipo_envio' => $detalleEnvio->tipo_envio,
                    'direccion' => $detalleEnvio->direccion,
                    'referencia' => $detalleEnvio->referencia,
                    'peso_total' => $detalleEnvio->peso_total,
                    'costo_envio' => $detalleEnvio->costo_envio,
                    'estado_envio' => $detalleEnvio->estado_envio,
                    'created_at' => $detalleEnvio->created_at instanceof \Carbon\Carbon 
                        ? $detalleEnvio->created_at->format('Y-m-d H:i:s') 
                        : $detalleEnvio->created_at,
                    'updated_at' => $detalleEnvio->updated_at instanceof \Carbon\Carbon 
                        ? $detalleEnvio->updated_at->format('Y-m-d H:i:s') 
                        : $detalleEnvio->updated_at,
                ],
                'ciudad_destino' => [
                    'id' => $detalleEnvio->ciudad->id,
                    'nombre' => $detalleEnvio->ciudad->nombre,
                    'precio_base' => $detalleEnvio->ciudad->precio_envio ?? null,
                ],
                'orden' => [
                    'id' => $detalleEnvio->orden->id,
                    'estado' => $detalleEnvio->orden->estado,
                    'monto_total' => $total,
                    'descuento_total' => $descuentoTotal,
                    'fecha_entrega' => $detalleEnvio->orden->fecha_entrega instanceof \Carbon\Carbon 
                        ? $detalleEnvio->orden->fecha_entrega->format('Y-m-d') 
                        : $detalleEnvio->orden->fecha_entrega,
                    'estado_pago' => $detalleEnvio->orden->estado_pago,
                ],
                'productos' => $detalleEnvio->orden->detalles->map(function ($detalle) {
                    $precioReal = $detalle->precio_unitario + $detalle->descuento_unitario;
                    
                    return [
                        'producto_id' => $detalle->detalleProducto->producto->id,
                        'nombre' => $detalle->detalleProducto->producto->nombre,
                        'descripcion' => $detalle->detalleProducto->producto->descripcion,
                        'imagen' => $detalle->detalleProducto->imagen_url 
                            ? asset('storage/'.$detalle->detalleProducto->imagen_url)
                            : asset('images/default-product.png'),
                        'cantidad' => $detalle->cantidad,
                        'talla' => $detalle->talla->nombre,
                        'color' => $detalle->detalleProducto->color->nombre, 
                        'codigo_hex' => $detalle->detalleProducto->color->codigo_hex,
                        'precio_unitario' => $detalle->precio_unitario,
                        'precio_real' => $precioReal,
                        'descuento_unitario' => $detalle->descuento_unitario,
                        'subtotal' => $detalle->subtotal,
                    ];
                }),
                'resumen' => [
                    'subtotal_sin_descuento' => $subtotalSinDescuento,
                    'subtotal_con_descuento' => $subtotalConDescuento,
                    'descuento_total' => $descuentoTotal,
                    'envio' => $detalleEnvio->costo_envio,
                    'total' => $total
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Envíos obtenidos correctamente',
            'usuario' => $usuarioData,
            'total' => $envios->count(),
            'data' => $enviosFormateados
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los envíos',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null
        ], 500);
    }
}

}
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
                'orden.detalles.detalleProducto.producto:id,nombre,descripcion',
                'orden.detalles.detalleProducto.color:id,nombre,codigo_hex', 
                'orden.detalles.talla:id,nombre',
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

            // 5. Formatear respuesta
            $enviosFormateados = $envios->map(function ($detalleEnvio) use ($usuarioData) {
                return [
                    'envio' => [
                        'id' => $detalleEnvio->id,
                        'tipo_envio' => $detalleEnvio->tipo_envio,
                        'direccion' => $detalleEnvio->direccion,
                        'referencia' => $detalleEnvio->referencia,
                        'peso_total' => $detalleEnvio->peso_total,
                        'costo_envio' => $detalleEnvio->costo_envio,
                        'estado_envio' => $detalleEnvio->estado_envio,
                        'created_at' => $detalleEnvio->created_at,
                        'updated_at' => $detalleEnvio->updated_at,
                    ],
                    'ciudad_destino' => [
                        'id' => $detalleEnvio->ciudad->id,
                        'nombre' => $detalleEnvio->ciudad->nombre,
                        'precio_base' => $detalleEnvio->ciudad->precio_envio ?? null,
                    ],
                    'orden' => [
                        'id' => $detalleEnvio->orden->id,
                        'estado' => $detalleEnvio->orden->estado,
                        'monto_total' => $detalleEnvio->orden->monto_total,
                        'descuento_total' => $detalleEnvio->orden->descuento_total,
                        'fecha_entrega' => $detalleEnvio->orden->fecha_entrega,
                        'estado_pago' => $detalleEnvio->orden->estado_pago,
                    ],
                    'productos' => $detalleEnvio->orden->detalles->map(function ($detalle) {
                        return [
                            'producto_id' => $detalle->detalleProducto->producto->id,
                            'nombre' => $detalle->detalleProducto->producto->nombre,
                            'imagen' => $detalle->detalleProducto->imagen_url 
                                ? asset('storage/'.$detalle->detalleProducto->imagen_url)
                                : asset('images/default-product.png'),
                            'cantidad' => $detalle->cantidad,
                            'talla' => $detalle->talla->nombre,
                            'color' => $detalle->detalleProducto->color->nombre, 
                            'codigo_hex' => $detalle->detalleProducto->color->codigo_hex,
                            'precio_unitario' => $detalle->precio_unitario,
                            'subtotal' => $detalle->subtotal,
                        ];
                    }),
                    'resumen' => [
                        'subtotal' => $detalleEnvio->orden->monto_total + $detalleEnvio->orden->descuento_total,
                        'descuento' => $detalleEnvio->orden->descuento_total,
                        'envio' => $detalleEnvio->costo_envio,
                        'total' => $detalleEnvio->orden->monto_total
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Envíos obtenidos correctamente',
                'usuario' => $usuarioData, // Datos del usuario una sola vez
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
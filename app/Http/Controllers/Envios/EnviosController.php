<?php

namespace App\Http\Controllers\Envios;

use App\Models\Empresa;
use App\Models\DetalleEnvio;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class EnviosController extends Controller
{
    /**
     * Obtener datos completos de un envío específico
     */
    public function mostrarEnvio($id)
    {
        try {
            $detalleEnvio = DetalleEnvio::with([
                'orden.usuario:id,nombre,apellido,celular,email', // Solo campos necesarios del usuario
                'ciudad:id,nombre,precio_envio', // Datos de la ciudad
                'orden.detalles.detalleProducto.producto:id,nombre', // Productos de la orden
                'orden.detalles.talla:id,nombre', // Tallas
                'orden.detalles.detalleProducto.color:id,nombre',
                
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
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
                        'precio_base' => $detalleEnvio->ciudad->precio_envio,
                    ],
                    'orden' => [
                        'id' => $detalleEnvio->orden->id,
                        'estado' => $detalleEnvio->orden->estado,
                        'monto_total' => $detalleEnvio->orden->monto_total,
                        'descuento_total' => $detalleEnvio->orden->descuento_total,
                        'fecha_entrega' => $detalleEnvio->orden->fecha_entrega,
                        'estado_pago' => $detalleEnvio->orden->estado_pago,
                    ],
                    'usuario' => [
                        'id' => $detalleEnvio->orden->usuario->id,
                        'nombre' => $detalleEnvio->orden->usuario->nombre,
                        'apellido' => $detalleEnvio->orden->usuario->apellido,
                        'celular' => $detalleEnvio->orden->usuario->celular ?? 'No especificado',
                        'email' => $detalleEnvio->orden->usuario->email,
                    ],
                    'productos' => $detalleEnvio->orden->detalles->map(function ($detalle) {
                        return [
                            'producto_id' => $detalle->detalleProducto->producto->id,
                            'nombre' => $detalle->detalleProducto->producto->nombre,
                            'cantidad' => $detalle->cantidad,
                            'talla' => $detalle->talla->nombre,
                            'color' => $detalle->detalleProducto->color->nombre ?? 'No especificado', 
                            'precio_unitario' => $detalle->precio_unitario,
                            'subtotal' => $detalle->subtotal,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Envío no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

public function mostrarEnvioPDF($id)
{
    try {
        $detalleEnvio = DetalleEnvio::with([
            'orden.usuario:id,nombre,apellido,celular,email',
            'ciudad:id,nombre,precio_envio',
            'orden.detalles.detalleProducto.producto:id,nombre',
            'orden.detalles.detalleProducto.color:id,nombre',
            'orden.detalles.talla:id,nombre'
        ])->findOrFail($id);
        $empresa = Empresa::first();
        $data = [
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
                'precio_base' => $detalleEnvio->ciudad->precio_envio,
            ],
            'orden' => [
                'id' => $detalleEnvio->orden->id,
                'estado' => $detalleEnvio->orden->estado,
                'monto_total' => $detalleEnvio->orden->monto_total,
                'descuento_total' => $detalleEnvio->orden->descuento_total,
                'fecha_entrega' => $detalleEnvio->orden->fecha_entrega,
                'estado_pago' => $detalleEnvio->orden->estado_pago,
            ],
            'usuario' => [
                'id' => $detalleEnvio->orden->usuario->id,
                'nombre' => $detalleEnvio->orden->usuario->nombre,
                'apellido' => $detalleEnvio->orden->usuario->apellido,
                'celular' => $detalleEnvio->orden->usuario->celular ?? 'No especificado',
                'email' => $detalleEnvio->orden->usuario->email,
            ],
            'productos' => $detalleEnvio->orden->detalles->map(function ($detalle) {
                return [
                    'producto_id' => $detalle->detalleProducto->producto->id,
                    'nombre' => $detalle->detalleProducto->producto->nombre,
                    'cantidad' => $detalle->cantidad,
                    'talla' => $detalle->talla->nombre,
                    'color' => $detalle->detalleProducto->color->nombre ?? 'No especificado', 
                    'precio_unitario' => $detalle->precio_base,
                    'subtotal' => $detalle->subtotal,
                ];
            }),
            'empresa' => [
                'nombre' => $empresa->nombre,
                'direccion' => $empresa->direccion,
                'telefono' => $empresa->telefono,
                'celular' => $empresa->celular,
                'email' => $empresa->email,
                'descripcion' => $empresa->descripcion,
                'facebook' => $empresa->facebook,
                'instagram' => $empresa->instagram,
                'logo' => $empresa->logo,
            ]
        ];

        // Generar el PDF
        $pdf = Pdf::loadView('pdf.envio', compact('data'));
        
        // Configurar el PDF
        $pdf->setPaper('A4', 'portrait');
        
        // Descargar el PDF
        return $pdf->download('envio_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf');

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al generar el PDF del envío',
            'error' => $e->getMessage()
        ], 404);
    }
}
    /**
     * Listar todos los envíos con información básica
     */
    public function listarEnvios()
    {
        try {
            $envios = DetalleEnvio::with([
                'orden.usuario:id,nombre,email',
                'ciudad:id,nombre'
            ])->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $envios->map(function ($envio) {
                    return [
                        'id' => $envio->id,
                        'orden_id' => $envio->orden->id,
                        'usuario' => [
                            'id' => $envio->orden->usuario->id,
                            'nombre' => $envio->orden->usuario->nombre,
                            'email' => $envio->orden->usuario->email,
                        ],
                        'tipo_envio' => $envio->tipo_envio,
                        'ciudad_destino' => $envio->ciudad->nombre ?? 'No especificada',
                        'estado_envio' => $envio->estado_envio,
                        'costo_envio' => $envio->costo_envio,
                        'fecha_creacion' => $envio->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los envíos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener envíos por usuario específico
     */
    public function enviosPorUsuario($usuarioId)
    {
        try {
            $envios = DetalleEnvio::whereHas('orden', function($query) use ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            })->with([
                'orden:id,estado,monto_total,fecha_entrega',
                'ciudad:id,nombre'
            ])->orderBy('created_at', 'desc')->get();

            if ($envios->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron envíos para este usuario',
                    'data' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $envios->map(function ($envio) {
                    return [
                        'id' => $envio->id,
                        'orden_id' => $envio->orden->id,
                        'estado_orden' => $envio->orden->estado,
                        'monto_total' => $envio->orden->monto_total,
                        'fecha_entrega' => $envio->orden->fecha_entrega,
                        'tipo_envio' => $envio->tipo_envio,
                        'direccion' => $envio->direccion,
                        'ciudad_destino' => $envio->ciudad->nombre ?? 'No especificada',
                        'estado_envio' => $envio->estado_envio,
                        'costo_envio' => $envio->costo_envio,
                        'peso_total' => $envio->peso_total,
                        'fecha_envio' => $envio->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los envíos del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de envío
     */
    public function actualizarEstadoEnvio(Request $request, $id)
    {
        try {
            $request->validate([
                'estado_envio' => 'required|string|in:pendiente,enviado,entregado'
            ]);

            $detalleEnvio = DetalleEnvio::findOrFail($id);
            $detalleEnvio->update([
                'estado_envio' => $request->estado_envio
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de envío actualizado correctamente',
                'data' => [
                    'id' => $detalleEnvio->id,
                    'estado_envio' => $detalleEnvio->estado_envio,
                    'updated_at' => $detalleEnvio->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado del envío',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

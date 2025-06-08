<?php

namespace App\Http\Controllers\Reportes;

use App\Models\User;
use App\Models\Orden;
use App\Models\Empresa;
use App\Models\DetalleEnvio;
use App\Models\DetalleOrden;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class ReportesController extends Controller
{
 
    public function ingresosMensuales(Request $request) {
        // Validar los parámetros (puedes pasar un rango de fechas o solo un mes)
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
    
        // Obtenemos el rango de fechas que recibe el frontend
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
    
        // Consultar los ingresos dentro de las fechas proporcionadas
        $ingresos = Orden::selectRaw('MONTH(fecha_entrega) as mes, SUM(monto_total) as ingresos')
                        ->whereBetween('fecha_entrega', [$startDate, $endDate])
                        ->groupBy('mes')
                        ->get();
    
        // Transformar la respuesta para incluir el nombre del mes
        $ingresosMensuales = $ingresos->map(function ($item) {
            return [
                'mes' => Carbon::createFromFormat('m', $item->mes)->format('F'),
                'ingresos' => $item->ingresos
            ];
        });
    
        // Obtener el total de ingresos
        $totalIngresos = $ingresosMensuales->sum('ingresos');
    
        // Devolver la respuesta con los ingresos y el total
        return response()->json([
            'ingresos_mensuales' => $ingresosMensuales,
            'total_ingresos' => $totalIngresos
        ]);
    }
    
 public function listarTodosEnvios()
{
    try {
        $envios = DetalleEnvio::with([
            'orden.usuario:id,nombre,apellido,celular,email',
            'ciudad:id,nombre,precio_envio',
            'orden.detalles.detalleProducto.producto:id,nombre',
            'orden.detalles.detalleProducto.color:id,nombre',
            'orden.detalles.talla:id,nombre'
        ])->get();

        $empresa = Empresa::first();
        
        // Transformamos los envíos sin incluir la empresa en cada uno
        $enviosTransformados = $envios->map(function ($detalleEnvio) {
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
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'empresa' => $empresa ? [
                    'nombre' => $empresa->nombre,
                    'direccion' => $empresa->direccion,
                    'telefono' => $empresa->telefono,
                    'celular' => $empresa->celular,
                    'email' => $empresa->email,
                    'descripcion' => $empresa->descripcion,
                    'facebook' => $empresa->facebook,
                    'instagram' => $empresa->instagram,
                    'logo' => $empresa->logo,
                ] : null,
                'envios' => $enviosTransformados
            ],
            'count' => $envios->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los envíos',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function InformePedidos()
{
    try {
        // Obtener información de la empresa
        $empresa = Empresa::first();
        
        $envios = DetalleEnvio::with([
            'orden.usuario:id,nombre,apellido,celular,email',
            'ciudad:id,nombre',
            'orden.detalles.detalleProducto.producto:id,nombre,descripcion',
            'orden.detalles.detalleProducto.color:id,nombre',
            'orden.detalles.talla:id,nombre'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Transformamos los envíos para el informe
        // $informePedidos = $envios->map(function ($detalleEnvio) {
        //     return [
        //         'id_pedido' => $detalleEnvio->orden->id,
        //         'fecha_pedido' => $detalleEnvio->created_at->format('Y-m-d H:i:s'),
        //         'cliente' => [
        //             'nombre_completo' => $detalleEnvio->orden->usuario->nombre . ' ' . $detalleEnvio->orden->usuario->apellido,
        //             'telefono' => $detalleEnvio->orden->usuario->celular ?? 'No especificado',
        //             'email' => $detalleEnvio->orden->usuario->email,
        //         ],
        //         'direccion_entrega' => [
        //             'ciudad' => $detalleEnvio->ciudad->nombre,
        //             'direccion' => $detalleEnvio->direccion,
        //             'referencia' => $detalleEnvio->referencia ?? 'Sin referencia',
        //         ],
        //         'tipo_entrega' => $detalleEnvio->tipo_envio,
        //         'detalle_productos' => $detalleEnvio->orden->detalles->map(function ($detalle) {
        //              $precioReal = $detalle->precio_unitario;
                        
        //                 // Si hay descuento aplicado, sumar el descuento al precio unitario para obtener el precio original
        //                 if ($detalle->descuento_unitario > 0) {
        //                     $precioReal = $detalle->precio_unitario + $detalle->descuento_unitario;
        //                 }
        //             return [
        //                 'producto_id' => $detalle->detalleProducto->producto->id,
        //                 'producto' => $detalle->detalleProducto->producto->nombre,
        //                 'descripcion' => $detalle->detalleProducto->producto->descripcion ?? 'Sin descripción',
        //                 'cantidad' => $detalle->cantidad,
        //                 'talla' => $detalle->talla->nombre,
        //                 'color' => $detalle->detalleProducto->color->nombre ?? 'No especificado',
        //                 'precio_unitario' => $detalle->precio_unitario, // Precio con descuento
        //                 'precio_real' => $precioReal, // Precio sin descuento
        //                 'descuento_unitario' => $detalle->descuento_unitario, // Descuento aplicado
        //                 'subtotal' => $detalle->subtotal
        //             ];
        //         }),
        //         'resumen_pedido' => [
        //             'subtotal' => $detalleEnvio->orden->monto_total + $detalleEnvio->orden->descuento_total,
        //             'descuento' => $detalleEnvio->orden->descuento_total,
        //             'costo_envio' => $detalleEnvio->costo_envio,
        //             'total' => $detalleEnvio->orden->monto_total,
        //             'estado_pago' => $detalleEnvio->orden->estado_pago,
        //         ],
        //         'estado_pedido' => [
        //             'estado_envio' => $detalleEnvio->estado_envio,
        //             'estado_orden' => $detalleEnvio->orden->estado,
        //             'fecha_entrega_estimada' => $detalleEnvio->orden->fecha_entrega ?? 'No especificada',
        //         ],
        //         'peso_total' => $detalleEnvio->peso_total,
        //     ];
        // });
        $informePedidos = $envios->map(function ($detalleEnvio) {
    // Calcular el subtotal sumando los subtotales de cada producto (sin descuentos)
    $subtotalSinDescuento = $detalleEnvio->orden->detalles->sum(function ($detalle) {
        return ($detalle->precio_unitario + $detalle->descuento_unitario) * $detalle->cantidad;
    });
    
    // Calcular el descuento total sumando los descuentos de cada producto
    $descuentoTotal = $detalleEnvio->orden->detalles->sum(function ($detalle) {
        return $detalle->descuento_unitario * $detalle->cantidad;
    });
    
    // El subtotal con descuento ya viene en monto_total (pero lo calculamos por seguridad)
    $subtotalConDescuento = $subtotalSinDescuento - $descuentoTotal;
    
    return [
        'id_pedido' => $detalleEnvio->orden->id,
        'fecha_pedido' => $detalleEnvio->created_at->format('Y-m-d H:i:s'),
        'cliente' => [
            'nombre_completo' => $detalleEnvio->orden->usuario->nombre . ' ' . $detalleEnvio->orden->usuario->apellido,
            'telefono' => $detalleEnvio->orden->usuario->celular ?? 'No especificado',
            'email' => $detalleEnvio->orden->usuario->email,
        ],
        'direccion_entrega' => [
            'ciudad' => $detalleEnvio->ciudad->nombre,
            'direccion' => $detalleEnvio->direccion,
            'referencia' => $detalleEnvio->referencia ?? 'Sin referencia',
        ],
        'tipo_entrega' => $detalleEnvio->tipo_envio,
        'detalle_productos' => $detalleEnvio->orden->detalles->map(function ($detalle) {
            $precioReal = $detalle->precio_unitario + $detalle->descuento_unitario;
            
            return [
                'producto_id' => $detalle->detalleProducto->producto->id,
                'producto' => $detalle->detalleProducto->producto->nombre,
                'descripcion' => $detalle->detalleProducto->producto->descripcion ?? 'Sin descripción',
                'cantidad' => $detalle->cantidad,
                'talla' => $detalle->talla->nombre,
                'color' => $detalle->detalleProducto->color->nombre ?? 'No especificado',
                'precio_unitario' => $detalle->precio_unitario, // Precio con descuento
                'precio_real' => $precioReal, // Precio sin descuento
                'descuento_unitario' => $detalle->descuento_unitario, // Descuento aplicado
                'subtotal' => $detalle->subtotal
            ];
        }),
        'resumen_pedido' => [
            'subtotal_sin_descuento' => $subtotalSinDescuento,
            'subtotal' => $subtotalConDescuento,
            'descuento_total' => $descuentoTotal,
            'costo_envio' => $detalleEnvio->costo_envio,
            'total' => $subtotalConDescuento + $detalleEnvio->costo_envio,
            'estado_pago' => $detalleEnvio->orden->estado_pago,
        ],
        'estado_pedido' => [
            'estado_envio' => $detalleEnvio->estado_envio,
            'estado_orden' => $detalleEnvio->orden->estado,
            'fecha_entrega_estimada' => $detalleEnvio->orden->fecha_entrega ?? 'No especificada',
        ],
        'peso_total' => $detalleEnvio->peso_total,
    ];
});

        return response()->json([
            'success' => true,
            'empresa' => $empresa ? [
                'nombre' => $empresa->nombre,
                'direccion' => $empresa->direccion,
                'telefono' => $empresa->telefono,
                'celular' => $empresa->celular,
                'email' => $empresa->email,
                'logo' => $empresa->logo,
                'descripcion' => $empresa->descripcion,
                'redes_sociales' => [
                    'facebook' => $empresa->facebook,
                    'instagram' => $empresa->instagram
                ]
            ] : null,
            'pedidos' => $informePedidos,
            'total_pedidos' => $envios->count(),
            'fecha_generacion' => now()->format('Y-m-d H:i:s'),
            'resumen_general' => [
                'total_ventas' => $envios->sum('orden.monto_total'),
                'total_envios' => $envios->sum('costo_envio'),
                'pedidos_pendientes' => $envios->where('estado_envio', 'pendiente')->count(),
                'pedidos_completados' => $envios->where('estado_envio', 'completado')->count()
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al generar el informe de pedidos',
            'error' => $e->getMessage()
        ], 500);
    }
}

}

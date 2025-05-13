<?php

namespace App\Http\Controllers\Descuento;

use App\Models\Descuento;

use Illuminate\Http\Request;
use App\Models\DetalleProducto;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DescuentoController extends Controller
{
    public function index()
    {
        $descuentos = Descuento::with('detallesProductos')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $descuentos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => ['required', Rule::in(['porcentaje', 'monto_fijo'])],
            'valor' => 'required|numeric|min:0',
            'activo' => 'boolean',
            'cantidad_minima' => 'required|integer|min:1',
            'aplica_todos_productos' => 'required|boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'detalles_productos' => 'required_if:aplica_todos_productos,false|array',
            'detalles_productos.*' => 'exists:detalles_productos,id'
        ]);

        try {
            DB::beginTransaction();

            $descuento = Descuento::create($validated);

            if (!$validated['aplica_todos_productos'] && isset($validated['detalles_productos'])) {
                $descuento->detallesProductos()->attach($validated['detalles_productos']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Descuento creado exitosamente',
                'data' => $descuento->load('detallesProductos')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el descuento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Descuento $descuento)
    {
        return response()->json([
            'status' => 'success',
            'data' => $descuento->load('detallesProductos')
        ]);
    }

    public function update(Request $request, Descuento $descuento)
    {
        $validated = $request->validate([
            'nombre' => 'string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => [Rule::in(['porcentaje', 'monto_fijo'])],
            'valor' => 'numeric|min:0',
            'activo' => 'boolean',
            'cantidad_minima' => 'integer|min:1',
            'aplica_todos_productos' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'detalles_productos' => 'array',
            'detalles_productos.*' => 'exists:detalles_productos,id'
        ]);

        try {
            DB::beginTransaction();

            $descuento->update($validated);

            if (isset($validated['detalles_productos'])) {
                $descuento->detallesProductos()->sync($validated['detalles_productos']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Descuento actualizado exitosamente',
                'data' => $descuento->load('detallesProductos')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el descuento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Descuento $descuento)
    {
        try {
            DB::beginTransaction();
            
            $descuento->detallesProductos()->detach();
            $descuento->delete();
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Descuento eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el descuento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleActive(Descuento $descuento)
    {
        try {
            $descuento->update(['activo' => !$descuento->activo]);

            return response()->json([
                'status' => 'success',
                'message' => 'Estado del descuento actualizado exitosamente',
                'data' => $descuento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el estado del descuento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function aplicarDescuento(Request $request)
    {
        $validated = $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:detalles_productos,id',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);
    
        try {
            // Sumar el total de unidades compradas
            $cantidadTotal = collect($validated['productos'])->sum('cantidad');
    
            // Obtener descuentos globales
            $descuentosGlobales = Descuento::where('activo', true)
                ->where('cantidad_minima', '<=', $cantidadTotal)
                ->where('aplica_todos_productos', true)
                ->get();
    
            // Obtener IDs de productos seleccionados
            $productosIds = collect($validated['productos'])->pluck('id')->toArray();
    
            // Obtener descuentos específicos para los productos seleccionados
            $descuentosEspecificos = Descuento::where('activo', true)
                ->where('cantidad_minima', '<=', $cantidadTotal)
                ->where('aplica_todos_productos', false)
                ->whereHas('detallesProductos', function ($query) use ($productosIds) {
                    $query->whereIn('detalle_producto_id', $productosIds);
                })
                ->get();
    
            // Combinar descuentos válidos
            $descuentos = $descuentosGlobales->merge($descuentosEspecificos)
                ->filter(fn($descuento) => $descuento->esValido());
    
            // Calcular precios de los productos
            $productosConDescuento = [];
            $subtotalGeneral = 0;
    
            foreach ($validated['productos'] as $productoData) {
                $detalleProducto = DetalleProducto::find($productoData['id']);
                $subtotalProducto = $detalleProducto->precio_base * $productoData['cantidad'];
                $subtotalGeneral += $subtotalProducto;
    
                // Filtrar los descuentos específicos para este producto
                $descuentosProducto = $descuentosEspecificos->filter(function ($descuento) use ($detalleProducto) {
                    return $descuento->detallesProductos->contains('id', $detalleProducto->id);
                });
    
                // Separar descuentos por monto fijo y porcentaje
                $montoFijo = $descuentosProducto->where('tipo', 'monto_fijo')->sum(fn($descuento) => $descuento->valor);
                $porcentaje = $descuentosProducto->where('tipo', 'porcentaje')->sum('valor');
    
                // Calcular descuento total (monto fijo + porcentaje aplicado al precio base)
                $descuentoEspecifico = $montoFijo + (($porcentaje / 100) * $subtotalProducto);
    
                $productosConDescuento[] = [
                    'producto_id' => $detalleProducto->id,
                    'nombre' => $detalleProducto->producto->nombre,
                    'cantidad' => $productoData['cantidad'],
                    'precio_original' => $subtotalProducto,
                    'precio_base' => $detalleProducto->precio_base,
                    'descuento_especifico' => $descuentoEspecifico
                ];
            }
    
            // Calcular descuentos globales (separando monto fijo y porcentaje)
            $descuentoGlobalTotal = 0;
            foreach ($descuentosGlobales as $descuento) {
                if ($descuento->tipo == 'monto_fijo') {
                    $descuentoGlobalTotal += $descuento->valor * $cantidadTotal;
                } elseif ($descuento->tipo == 'porcentaje') {
                    $descuentoGlobalTotal += ($descuento->valor / 100) * $subtotalGeneral;
                }
            }
    
            // Aplicar y distribuir descuentos globales
            $descuentoTotal = 0;
            foreach ($productosConDescuento as &$producto) {
                // Aplicar descuento global proporcionalmente
                $proporcion = $producto['precio_original'] / $subtotalGeneral;
                $descuentoGlobal = round($descuentoGlobalTotal * $proporcion, 2);
    
                // Combinar descuentos globales y específicos
                $producto['descuento'] = $producto['descuento_especifico'] + $descuentoGlobal;
                $producto['precio_final'] = $producto['precio_original'] - $producto['descuento'];
                $descuentoTotal += $producto['descuento'];
    
                // Limpiar campo temporal
                unset($producto['descuento_especifico']);
            }
    
            // Respuesta con datos procesados
            return response()->json([
                'status' => 'success',
                'descuento_aplicado' => $descuentoTotal > 0,
                'cantidad_total' => $cantidadTotal,
                'descuento_total' => $descuentoTotal,
                'productos' => $productosConDescuento
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al calcular el descuento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

}

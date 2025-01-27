<?php

namespace App\Http\Controllers\Producto;

use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Models\DetalleProducto;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DetalleProductoController extends Controller
{
    public function all()
    {
        try {
            // Usamos la relación `tallas` en lugar de `talla`, ya que ahora se usa la tabla intermedia
            $variants = DetalleProducto::with(['producto', 'color', 'tallas'])->get(); // Asegúrate de usar 'tallas' en plural
            
            return response()->json([
                'status' => true,
                'message' => 'Lista de variantes obtenida exitosamente',
                'data' => $variants
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las variantes',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Crear un nuevo detalle de producto
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'color_id' => 'required|exists:colores,id',
            'tallas' => 'required|array', // 'tallas' sea un array
            'tallas.*' => 'exists:tallas,id', // Cada ID de talla debe existir en la tabla `tallas`
            'precio_base' => 'required|numeric|min:0',
          
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para la imagen
        ]);

        $detalleProductoData = $request->only(['producto_id', 'color_id', 'talla_id', 'precio_base']);

        // Subir la imagen si existe
        if ($request->hasFile('imagen_url')) {
            $image = $request->file('imagen_url');
            $imagePath = $image->store('imagenes_productos', 'public'); // Se guarda en el disco público
            $detalleProductoData['imagen_url'] = $imagePath;
        }

        // Crear el detalle del producto
        $detalleProducto = DetalleProducto::create($detalleProductoData);

        // Asociar las tallas con el detalle del producto mediante la tabla intermedia
        $detalleProducto->tallas()->attach($request->input('tallas'));

        return response()->json([
            'status' => true,
            'message' => 'Detalle de producto creado exitosamente',
            'data' => $detalleProducto->load('tallas') // Cargar las tallas asociadas para incluir en la respuesta
        ], 201);
    }

    // Mostrar un detalle específico del producto por ID
    public function show($id)
    {
        $detalle = DetalleProducto::with(['producto', 'color', 'talla'])->find($id);

        if (!$detalle) {
            return response()->json([
                'status' => false,
                'message' => 'Detalle de producto no encontrado',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detalle de producto encontrado',
            'data' => $detalle
        ], 200);
    }

    
    public function update(Request $request, $id)
    {
        // Validar que el ID exista
        $detalleProducto = DetalleProducto::find($id);
    
        if (!$detalleProducto) {
            return response()->json(['error' => 'Detalle de producto no encontrado'], 404);
        }
    
        // Validar los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'producto_id' => 'nullable|exists:productos,id',
            'color_id' => 'nullable|exists:colores,id',
            'tallas' => 'nullable|array',
            'tallas.*' => 'exists:tallas,id',
            'precio_base' => 'nullable|numeric|min:0',
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        // Actualizar los campos que se reciban
        if ($request->has('producto_id')) {
            $detalleProducto->producto_id = $request->input('producto_id');
        }
    
        if ($request->has('color_id')) {
            $detalleProducto->color_id = $request->input('color_id');
        }
    
        if ($request->has('precio_base')) {
            $detalleProducto->precio_base = $request->input('precio_base');
        }
    
        // Sincronizar las tallas asociadas
        if ($request->has('tallas')) {
            $detalleProducto->tallas()->sync($request->input('tallas'));
        }
    
        // Manejar la actualización de la imagen
        if ($request->hasFile('imagen_url')) {
            // Eliminar la imagen antigua si existe
            if ($detalleProducto->imagen_url && Storage::disk('public')->exists($detalleProducto->imagen_url)) {
                Storage::disk('public')->delete($detalleProducto->imagen_url);
            }
    
            // Subir la nueva imagen
            $image = $request->file('imagen_url');
            $imagePath = $image->store('imagenes_productos', 'public');
            $detalleProducto->imagen_url = $imagePath;
        }
    
        // Guardar los cambios
        $detalleProducto->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Detalle de producto actualizado exitosamente',
            'data' => [
                'id' => $detalleProducto->id,
                'producto_id' => $detalleProducto->producto_id,
                'color_id' => $detalleProducto->color_id,
                'precio_base' => $detalleProducto->precio_base,
                'imagen_url' => $detalleProducto->imagen_url ? Storage::url($detalleProducto->imagen_url) : null, // Obtener la URL completa o null si no hay imagen
                'tallas' => $detalleProducto->tallas, // Devolver las tallas asociadas
            ]
        ], 200);
    }
    
   

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $detalleProducto = DetalleProducto::findOrFail($id);
    
    //         // Validar que los campos esenciales sean correctos para actualizar
    //         $request->validate([
    //             'producto_id' => 'required|exists:productos,id',
    //             'color_id' => 'required|exists:colores,id',
    //             'precio_base' => 'required|numeric|min:0',
    //             'tallas' => 'required|array',
    //             'tallas.*' => 'exists:tallas,id',
    //             'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         ]);
    
    //         // Si hay una nueva imagen, manejarla
    //         if ($request->hasFile('imagen_url')) {
    //             // Eliminar imagen antigua si existe
    //             if ($detalleProducto->imagen_url && Storage::disk('public')->exists($detalleProducto->imagen_url)) {
    //                 Storage::disk('public')->delete($detalleProducto->imagen_url);
    //             }
    
    //             // Guardar nueva imagen
    //             $image = $request->file('imagen_url');
    //             $imagePath = $image->store('imagenes_productos', 'public');
    //             $detalleProducto->imagen_url = $imagePath;
    //         }
    
    //         // Actualización del detalle de producto con datos restantes
    //         $detalleProducto->fill($request->only([
    //             'producto_id', 
    //             'color_id', 
    //             'precio_base'
    //         ]));
    
    //         DB::beginTransaction();
    
    //         try {
    //             // Guardar los cambios
    //             $detalleProducto->save();
    //             $detalleProducto->tallas()->sync($request->input('tallas')); // Actualizar tallas
    
    //             DB::commit();
    
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Producto actualizado exitosamente.',
    //                 'data' => $detalleProducto->load('tallas', 'producto', 'color')
    //             ], 200);
    
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             throw $e;
    //         }
    
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error de validación.',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error al actualizar el producto.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
   
    // public function update(Request $request, $id)
    // {
    //     try {
    //         Log::info('Request data inicial:', [
    //             'all' => $request->all(),
    //             'files' => $request->allFiles(),
    //             'has_file' => $request->hasFile('imagen_url'),
    //             'content_type' => $request->header('Content-Type'),
    //             'is_json' => $request->isJson()
    //         ]);
    
    //         $detalleProducto = DetalleProducto::findOrFail($id);
    
    //         // Preparar los datos para la validación
    //         $dataToValidate = [];
            
    //         // Manejar datos básicos
    //         $dataToValidate['producto_id'] = $request->input('producto_id');
    //         $dataToValidate['color_id'] = $request->input('color_id');
    //         $dataToValidate['precio_base'] = $request->input('precio_base');
            
    //         // Manejar tallas
    //         $tallas = $request->input('tallas');
    //         if (is_string($tallas)) {
    //             try {
    //                 $tallas = json_decode($tallas, true);
    //             } catch (\Exception $e) {
    //                 $tallas = null;
    //             }
    //         }
    //         $dataToValidate['tallas'] = $tallas;
    
    //         // Manejar imagen - solo incluir en la validación si hay un archivo
    //         if ($request->hasFile('imagen_url') && $request->file('imagen_url')->isValid()) {
    //             Log::info('Archivo de imagen detectado y válido');
    //             $dataToValidate['imagen_url'] = $request->file('imagen_url');
    //         } else {
    //             Log::info('No se detectó archivo de imagen válido', [
    //                 'hasFile' => $request->hasFile('imagen_url'),
    //                 'isValid' => $request->hasFile('imagen_url') ? $request->file('imagen_url')->isValid() : 'No hay archivo'
    //             ]);
    //         }
    
    //         Log::info('Datos a validar:', $dataToValidate);
    
    //         // Reglas de validación base
    //         $rules = [
    //             'producto_id' => 'required|exists:productos,id',
    //             'color_id' => 'required|exists:colores,id',
    //             'tallas' => 'required|array',
    //             'tallas.*' => 'exists:tallas,id',
    //             'precio_base' => 'required|numeric|min:0',
    //         ];
    
    //         // Agregar regla de imagen solo si se está enviando una imagen
    //         if (isset($dataToValidate['imagen_url'])) {
    //             $rules['imagen_url'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
    //         }
    
    //         // Validar
    //         $validated = Validator::make($dataToValidate, $rules)->validate();
    
    //         Log::info('Datos validados:', $validated);
    
    //         // Actualizar campos básicos
    //         $detalleProducto->producto_id = $dataToValidate['producto_id'];
    //         $detalleProducto->color_id = $dataToValidate['color_id'];
    //         $detalleProducto->precio_base = $dataToValidate['precio_base'];
    
    //         // Manejar la imagen solo si se envió un archivo válido
    //         if ($request->hasFile('imagen_url') && $request->file('imagen_url')->isValid()) {
    //             Log::info('Procesando archivo de imagen');
                
    //             // Eliminar imagen anterior si existe
    //             if ($detalleProducto->imagen_url && Storage::disk('public')->exists($detalleProducto->imagen_url)) {
    //                 Storage::disk('public')->delete($detalleProducto->imagen_url);
    //                 Log::info('Imagen anterior eliminada');
    //             }
    
    //             $image = $request->file('imagen_url');
    //             $imagePath = $image->store('imagenes_productos', 'public');
    //             Log::info('Nueva imagen guardada en: ' . $imagePath);
                
    //             $detalleProducto->imagen_url = $imagePath;
    //         }
    
    //         // Guardar cambios
    //         $detalleProducto->save();
    //         Log::info('Detalle de producto guardado');
    
    //         // Actualizar tallas
    //         if ($tallas) {
    //             $detalleProducto->tallas()->sync($tallas);
    //             Log::info('Tallas sincronizadas', ['tallas' => $tallas]);
    //         }
    
    //         $detalleProducto->load('tallas');
    
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Detalle de producto actualizado exitosamente',
    //             'data' => $detalleProducto
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         Log::error('Error en update:', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error al actualizar el detalle del producto',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function updatePartial(Request $request, $id)
    {
        try {
            // Encontrar el detalle del producto
            $detalleProducto = DetalleProducto::findOrFail($id);
    
            // Validar solo los campos proporcionados
            $validator = Validator::make($request->all(), [
                'producto_id' => 'sometimes|exists:productos,id',
                'color_id' => 'sometimes|exists:colores,id',
                'tallas' => 'sometimes|array',
                'tallas.*' => 'exists:tallas,id',
                'precio_base' => 'sometimes|numeric|min:0',
                'imagen_url' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Actualizar solo los campos proporcionados
            if ($request->has('producto_id')) {
                $detalleProducto->producto_id = $request->input('producto_id');
            }
            if ($request->has('color_id')) {
                $detalleProducto->color_id = $request->input('color_id');
            }
            if ($request->has('precio_base')) {
                $detalleProducto->precio_base = $request->input('precio_base');
            }
    
            // Manejar la actualización de la imagen si se proporciona
            if ($request->hasFile('imagen_url')) {
                // Eliminar la imagen anterior si existe
                if ($detalleProducto->imagen_url && Storage::disk('public')->exists($detalleProducto->imagen_url)) {
                    Storage::disk('public')->delete($detalleProducto->imagen_url);
                }
    
                // Subir la nueva imagen
                $image = $request->file('imagen_url');
                $imagePath = $image->store('imagenes_productos', 'public');
                $detalleProducto->imagen_url = $imagePath;
            }
    
            // Guardar los cambios
            $detalleProducto->save();
    
            // Actualizar las tallas si se proporcionaron
            if ($request->has('tallas')) {
                $detalleProducto->tallas()->sync($request->input('tallas'));
            }
    
            // Cargar las relaciones actualizadas
            $detalleProducto->load('tallas');
    
            return response()->json([
                'status' => true,
                'message' => 'Detalle de producto actualizado parcialmente',
                'data' => $detalleProducto
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Detalle de producto no encontrado'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el detalle del producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

// Eliminar un detalle de producto
    public function destroy($id)
    {
        $detalle = DetalleProducto::find($id);

        if (!$detalle) {
            return response()->json([
                'status' => false,
                'message' => 'Detalle de producto no encontrado',
            ], 404);
        }

        // Eliminar la imagen si existe
        if ($detalle->imagen_url) {
            Storage::disk('public')->delete($detalle->imagen_url);
        }

        // Eliminar el detalle de producto
        $detalle->delete();

        return response()->json([
            'status' => true,
            'message' => 'Detalle de producto eliminado exitosamente',
        ], 200);
    }

    public function index()
    {
        try {
            // Obtener las variantes con relaciones incluyendo `tallas` en lugar de `talla`
            $variants = DetalleProducto::with(['producto', 'color', 'tallas'])->get();
    
            // Agrupar por categoría utilizando colecciones de Laravel
            $agrupados = $variants->groupBy(function ($item) {
                return $item->producto->categoria_id;
            })->map(function ($items, $categoria_id) {
                return [
                    'categoria_id' => $categoria_id,
                    'productos' => $items->groupBy('producto_id')->map(function ($variantes, $producto_id) {
                        return [
                            'producto_id' => $producto_id,
                            'nombre' => $variantes->first()->producto->nombre,
                            'descripcion' => $variantes->first()->producto->descripcion,
                            'variantes' => $variantes->map(function ($variante) {
                                return [
                                    'id' => $variante->id,
                                    'color' => $variante->color->nombre ?? 'N/A',
                                    'color_codigo_hex' => $variante->color->codigo_hex ?? null,
                                    'tallas' => $variante->tallas->map(function ($talla) {
                                        return [
                                            'id' => $talla->id,
                                            'nombre' => $talla->nombre,
                                        ];
                                    })->values(), // Mapeo de tallas
                                    'precio_base' => $variante->precio_base,
                                    'imagen_url' => $variante->imagen_url,
                                ];
                            })->values(),
                        ];
                    })->values(),
                ];
            })->values();
    
            // Devolver la respuesta JSON
            return response()->json([
                'status' => true,
                'message' => 'Lista de productos agrupada por categoría obtenida exitosamente',
                'data' => $agrupados,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las variantes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

}


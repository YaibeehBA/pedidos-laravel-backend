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
            $variants = DetalleProducto::with(['producto', 'color', 'talla'])->get();
            
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
            'talla_id' => 'required|exists:tallas,id',
            'precio_base' => 'required|numeric|min:0',
          
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para la imagen
        ]);

        $detalleProductoData = $request->only(['producto_id', 'color_id', 'talla_id', 'precio_base','stock']);

        // Subir la imagen si existe
        if ($request->hasFile('imagen_url')) {
            $image = $request->file('imagen_url');
            $imagePath = $image->store('imagenes_productos', 'public'); // Se guarda en el disco público
            $detalleProductoData['imagen_url'] = $imagePath;
        }

        // Crear el detalle del producto
        $detalleProducto = DetalleProducto::create($detalleProductoData);

        return response()->json([
            'status' => true,
            'message' => 'Detalle de producto creado exitosamente',
            'data' => $detalleProducto
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
        // Validación de los campos
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'color_id' => 'required|exists:colores,id',
            'talla_id' => 'required|exists:tallas,id',
            'precio_base' => 'required|numeric|min:0',
           
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
        ]);
    
        // Buscar el detalle del producto con el id
        $detalleProducto = DetalleProducto::find($id);
    
        if (!$detalleProducto) {
            return response()->json([
                'status' => false,
                'message' => 'Detalle de producto no encontrado.',
            ], 404);
        }
    
        // Si el campo de imagen existe, manejar la carga de la nueva imagen
        if ($request->hasFile('imagen_url')) {
            $image = $request->file('imagen_url');
            $imagePath = $image->store('imagenes_productos', 'public'); // Guardar la imagen en almacenamiento público
            $request['imagen_url'] = $imagePath; // Asignamos el nuevo path de la imagen al request
        }
    
        // Dependiendo del verbo (PUT/PATCH), actualizamos el producto
        // Si es PATCH, no es necesario pasar todos los campos; podemos solo cambiar lo enviado
        $detalleProducto->update($request->only([
            'producto_id',
            'color_id',
            'talla_id',
            'precio_base',
            
            'imagen_url'
        ]));
    
        // Responder con los detalles actualizados
        return response()->json([
            'status' => true,
            'message' => 'Detalle de producto actualizado exitosamente.',
            'data' => $detalleProducto
        ], 200);
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
        // Obtener las variantes con relaciones
        $variants = DetalleProducto::with(['producto', 'color', 'talla'])->get();

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
                                'talla' => $variante->talla->nombre ?? 'N/A',
                                'precio_base' => $variante->precio_base,
                    
                                'imagen_url' => $variante->imagen_url
                            ];
                        })->values()
                    ];
                })->values()
            ];
        })->values();

        // Devolver la respuesta JSON
        return response()->json([
            'status' => true,
            'message' => 'Lista de productos agrupada por categoría obtenida exitosamente',
            'data' => $agrupados
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error al obtener las variantes',
            'error' => $e->getMessage()
        ], 500);
    }
}


}

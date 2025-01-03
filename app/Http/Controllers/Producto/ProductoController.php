<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        try {
            $products = Producto::with('category')->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Lista de productos obtenida exitosamente',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'precio_base' => 'required|numeric|min:0'
            ]);

            $product = Producto::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Producto::with(['category', 'variants.color', 'variants.size'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Producto encontrado exitosamente',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el producto',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'category_id' => 'exists:categories,id',
                'name' => 'string|max:255',
                'description' => 'string',
                'base_price' => 'numeric|min:0'
            ]);

            $product = Producto::findOrFail($id);
            $product->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Producto::findOrFail($id);
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Producto eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

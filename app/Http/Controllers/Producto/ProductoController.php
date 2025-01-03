<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    // Listar todos los productos
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        return response()->json([
            'status' => true,
            'message' => 'Lista de productos obtenida exitosamente',
            'data' => $productos,
        ], 200);
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $producto = Producto::create($request->only('nombre', 'descripcion', 'categoria_id'));

        return response()->json([
            'status' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $producto->load('categoria'),
        ], 201); // 201 indica "Creado".
    }

    // Mostrar un producto específico
    public function show($id)
    {
        $producto = Producto::with('categoria')->find($id);

        if (!$producto) {
            return response()->json([
                'status' => false,
                'message' => 'Producto no encontrado',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Producto obtenido exitosamente',
            'data' => $producto,
        ], 200);
    }

    // Actualizar un producto
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'status' => false,
                'message' => 'Producto no encontrado',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $producto->update($request->only('nombre', 'descripcion', 'categoria_id'));

        return response()->json([
            'status' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $producto->load('categoria'),
        ], 200);
    }

    // Eliminar un producto
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'status' => false,
                'message' => 'Producto no encontrado',
                'data' => null,
            ], 404);
        }

        $producto->delete();

        return response()->json([
            'status' => true,
            'message' => 'Producto eliminado exitosamente',
            'data' => $producto,
        ], 200);
    }
}

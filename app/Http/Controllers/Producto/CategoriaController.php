<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        try {
            $categorias = Categoria::all(); // Obtener todas las categorías

            return response()->json([
                'status' => true,
                'message' => 'Lista de categorías obtenida exitosamente',
                'data' => $categorias,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las categorías',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear una nueva categoría.
     */
    public function store(Request $request)
    {
        try {
            // Validación de los datos del request
            $request->validate([
                'nombre' => 'required|string|max:255|unique:categorias', // Nombre único para la categoría
                'descripcion' => 'nullable|string|max:1000',
            ]);

            // Crear la categoría
            $categoria = Categoria::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Categoría creada exitosamente',
                'data' => $categoria,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar una categoría específica por ID.
     */
    public function show($id)
    {
        try {
            // Buscar la categoría por ID
            $categoria = Categoria::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Categoría encontrada exitosamente',
                'data' => $categoria,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la categoría',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $id, // Asegurar que el nombre sea único
                'descripcion' => 'nullable|string|max:1000',
            ]);

            // Buscar la categoría
            $categoria = Categoria::findOrFail($id);

            // Actualizar la categoría
            $categoria->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data' => $categoria,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy($id)
    {
        try {
            // Buscar la categoría por ID
            $categoria = Categoria::findOrFail($id);

            // Eliminar la categoría
            $categoria->delete();

            return response()->json([
                'status' => true,
                'message' => 'Categoría eliminada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}


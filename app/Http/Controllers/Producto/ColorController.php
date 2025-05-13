<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function index()
    {
        try {
            $colors = Color::all();
            
            return response()->json([
                'status' => true,
                'message' => 'Lista de colores obtenida exitosamente',
                'data' => $colors
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los colores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:50|unique:colores',
                'codigo_hex' => 'nullable|string'

            ]);

            $color = Color::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Color creado exitosamente',
                'data' => $color
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $color = Color::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Color encontrado exitosamente',
                'data' => $color
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el color',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'string|max:50|unique:colores,nombre,' . $id,
                'codigo_hex' => 'nullable|string'



            ]);

            $color = Color::findOrFail($id);
            $color->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Color actualizado exitosamente',
                'data' => $color
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $color = Color::findOrFail($id);
            $color->delete();

            return response()->json([
                'status' => true,
                'message' => 'Color eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

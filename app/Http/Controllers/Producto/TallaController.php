<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Talla;

class TallaController extends Controller
{
    public function index()
    {
        try {
            $sizes = Talla::all();
            
            return response()->json([
                'status' => true,
                'message' => 'Lista de tallas obtenida exitosamente',
                'data' => $sizes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las tallas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:10|unique:tallas'
            ]);

            $size = Talla::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Talla creada exitosamente',
                'data' => $size
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear la talla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $size = Talla::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Talla encontrada exitosamente',
                'data' => $size
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la talla',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'string|max:10|unique:tallas,nombre,' . $id
            ]);

            $size = Talla::findOrFail($id);
            $size->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Talla actualizada exitosamente',
                'data' => $size
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la talla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $size = Talla::findOrFail($id);
            $size->delete();

            return response()->json([
                'status' => true,
                'message' => 'Talla eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar la talla',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

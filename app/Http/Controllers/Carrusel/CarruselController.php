<?php

namespace App\Http\Controllers\Carrusel;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Carrusel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarruselController extends Controller
{
    public function index()
    {
        return Carrusel::all();
    }

    // Guardar nueva imagen
    public function store(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('imagen')->store('carrusel', 'public');

        $carrusel = Carrusel::create([
            'imagen' => $path
        ]);

        return response()->json([
            'mensaje' => 'Imagen de carrusel guardada correctamente.',
            'data' => $carrusel
        ], 201);
    }

    // Mostrar una imagen específica
    public function show($id)
    {
        return Carrusel::findOrFail($id);
    }

    // Eliminar una imagen
    public function destroy($id)
{
    // Buscar el registro, si no existe devuelve 404 automáticamente
    $carrusel = Carrusel::find($id);

    if (!$carrusel) {
        return response()->json([
            'status' => false,
            'message' => 'Imagen del carrusel no encontrada',
        ], 404);
    }

    // Eliminar la imagen del storage si existe
    if ($carrusel->imagen && Storage::disk('public')->exists($carrusel->imagen)) {
        Storage::disk('public')->delete($carrusel->imagen);
    }

    // Eliminar el registro del carrusel
    $carrusel->delete();

    return response()->json([
        'status' => true,
        'message' => 'Imagen del carrusel eliminada exitosamente',
    ], 200);
}

    public function actualizar(Request $request, $id)
{
    $carrusel = Carrusel::find($id);

    if (!$carrusel) {
        return response()->json([
            'status' => false,
            'mensaje' => 'Carrusel no encontrado.',
        ], 404);
    }

   
    $validator = Validator::make($request->all(), [
        'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errores' => $validator->errors()
        ], 422);
    }

    // Eliminar imagen anterior si existe
    if ($carrusel->imagen && Storage::disk('public')->exists($carrusel->imagen)) {
        Storage::disk('public')->delete($carrusel->imagen);
    }

    // Guardar nueva imagen
    $path = $request->file('imagen')->store('carrusel', 'public');
    $carrusel->imagen = $path;
    $carrusel->save();

    return response()->json([
        'status' => true,
        'mensaje' => 'Imagen de carrusel actualizada correctamente.',
        'data' => [
            'id' => $carrusel->id,
            'imagen_url' => Storage::url($carrusel->imagen) // Devuelve URL completa para mostrar en el frontend
        ]
    ]);
}
}

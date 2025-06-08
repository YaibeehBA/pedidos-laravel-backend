<?php

namespace App\Http\Controllers\Consultas;

use App\Models\Consulta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsultasController extends Controller
{
    // 1. Crear consulta
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo'    => 'required|string|max:255',
            'correo_electronico' => 'required|email|max:255',
            'telefono'           => 'nullable|string|max:20',
            'mensaje'            => 'required|string',
        ]);

        $consulta = Consulta::create($validated);

        return response()->json(['message' => 'Consulta creada con éxito', 'data' => $consulta], 201);
    }

    // 2. Mostrar todas las consultas
    public function index()
    {
        return response()->json(Consulta::orderBy('created_at', 'desc')->get());
    }

    // 3. Mostrar una consulta por ID
    public function show($id)
    {
        $consulta = Consulta::findOrFail($id);
        return response()->json($consulta);
    }

    // 4. Marcar como leída
    public function marcarLeida($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->leido = true;
        $consulta->save();

        return response()->json(['message' => 'Consulta marcada como leída']);
    }

    // 5. Eliminar una consulta
    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->delete();

        return response()->json(['message' => 'Consulta eliminada']);
    }
}

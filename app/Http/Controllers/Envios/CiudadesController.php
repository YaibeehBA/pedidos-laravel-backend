<?php

namespace App\Http\Controllers\Envios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CiudadEnvio;

class CiudadesController extends Controller
{
    public function index()
    {
        return response()->json(CiudadEnvio::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:ciudad_envios,nombre',
            'precio_envio' => 'required|numeric|min:0',
        ]);

        $ciudad = CiudadEnvio::create($request->only('nombre', 'precio_envio'));

        return response()->json(['message' => 'Ciudad creada correctamente', 'ciudad' => $ciudad]);
    }

    public function show($id)
    {
        return response()->json(CiudadEnvio::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $ciudad = CiudadEnvio::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|unique:ciudad_envios,nombre,' . $ciudad->id,
            'precio_envio' => 'required|numeric|min:0',
        ]);

        $ciudad->update($request->only('nombre', 'precio_envio'));

        return response()->json(['message' => 'Ciudad actualizada correctamente', 'ciudad' => $ciudad]);
    }

    public function destroy($id)
    {
        $ciudad = CiudadEnvio::findOrFail($id);
        $ciudad->delete();

        return response()->json(['message' => 'Ciudad eliminada correctamente']);
    }
}

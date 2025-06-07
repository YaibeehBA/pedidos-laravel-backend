<?php

namespace App\Http\Controllers\Envios;

use Illuminate\Http\Request;
use App\Models\ConfiguracionEnvio;
use App\Http\Controllers\Controller;

class ConfiguracionEnvioController extends Controller
{
     public function index()
    {
        $config = ConfiguracionEnvio::first();
        return response()->json($config);
    }

    public function store(Request $request)
    {
        $request->validate([
            'precio_por_kg' => 'required|numeric|min:0',
        ]);

        $config = ConfiguracionEnvio::create([
            'precio_por_kg' => $request->precio_por_kg,
        ]);

        return response()->json(['message' => 'Configuración creada', 'configuracion' => $config]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'precio_por_kg' => 'required|numeric|min:0',
        ]);

        $config = ConfiguracionEnvio::first();

        if (!$config) {
            $config = ConfiguracionEnvio::create(['precio_por_kg' => $request->precio_por_kg]);
        } else {
            $config->update(['precio_por_kg' => $request->precio_por_kg]);
        }

        return response()->json(['message' => 'Configuración actualizada', 'configuracion' => $config]);
    }
}

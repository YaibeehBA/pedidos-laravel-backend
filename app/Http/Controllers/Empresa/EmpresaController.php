<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class EmpresaController extends Controller
{
      public function index()
    {
        return Empresa::all();
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'celular' => 'required|string|max:20',
            'email' => 'required|email|unique:empresas',
            'descripcion' => 'nullable|string',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nombre', 'direccion','referencia', 'telefono', 'celular',
            'email', 'descripcion', 'facebook', 'instagram'
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa = Empresa::create($data);

        return response()->json([
            'mensaje' => 'Perfil de empresa creado correctamente.',
            'data' => $empresa
        ], 201);
    }

    
    public function show($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json([
                'status' => false,
                'mensaje' => 'Empresa no encontrada.',
            ], 404);
        }
        return response()->json($empresa);
    }

    // Eliminar  perfil de la empresa
    public function destroy($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json([
                'status' => false,
                'mensaje' => 'Empresa no encontrada.',
            ], 404);
        }

        if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
            Storage::disk('public')->delete($empresa->logo);
        }

        $empresa->delete();

        return response()->json([
            'status' => true,
            'mensaje' => 'Empresa eliminada correctamente.',
        ]);
    }

    // Actualizar empresa usando POST
    public function actualizar(Request $request, $id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json([
                'status' => false,
                'mensaje' => 'Empresa no encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'direccion' => 'sometimes|required|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'telefono' => 'sometimes|required|string|max:20',
            'celular' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:empresas,email,' . $empresa->id,
            'descripcion' => 'nullable|string',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errores' => $validator->errors(),
            ], 422);
        }

        // Si se envía una nueva imagen, eliminar la anterior
        if ($request->hasFile('logo')) {
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $empresa->logo = $request->file('logo')->store('logos', 'public');
        }

        $empresa->fill($request->except('logo'));
        $empresa->save();

        return response()->json([
            'status' => true,
            'mensaje' => 'Perfil de empresa actualizado correctamente.',
            'data' => [
                'id' => $empresa->id,
                'nombre' => $empresa->nombre,
                'direccion' => $empresa->direccion,
                'referencia' => $empresa->referencia,
                'telefono' => $empresa->telefono,
                'celular' => $empresa->celular,
                'email' => $empresa->email,
                'descripcion' => $empresa->descripcion,
                'facebook' => $empresa->facebook,
                'instagram' => $empresa->instagram,
                'logo' => $empresa->logo,
                'logo_url' => $empresa->logo ? Storage::url($empresa->logo) : null,
            ]
        ]);

    }
}

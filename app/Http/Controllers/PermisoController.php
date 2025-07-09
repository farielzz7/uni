<?php
// app/Http/Controllers/PermisoController.php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    //  Listar todos los permisos
    public function index()
    {
        return response()->json(Permiso::all());
    }

    //  Mostrar un permiso por ID
    public function show($id)
    {
        $permiso = Permiso::findOrFail($id);
        return response()->json($permiso);
    }

    //  Crear nuevo permiso
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'clave_permiso' => 'required|string|max:100|unique:permisos'
        ]);

        $permiso = Permiso::create($validated);
        return response()->json($permiso, 201);
    }

    //  Actualizar permiso existente
    public function update(Request $request, $id)
    {
        $permiso = Permiso::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'clave_permiso' => 'sometimes|string|max:100|unique:permisos,clave_permiso,' . $permiso->id
        ]);

        $permiso->update($validated);
        return response()->json($permiso);
    }

    //  Eliminar permiso
    public function destroy($id)
    {
        $permiso = Permiso::findOrFail($id);
        $permiso->delete();
        return response()->json(['message' => 'Permiso eliminado correctamente.']);
    }
}

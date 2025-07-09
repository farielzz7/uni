<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    //  Listar servicios
    public function index()
    {

        return response()->json(servicio::all());
    }

    //  Mostrar servicio específico
    public function show($id)
    {
        $servicio = Servicio::findOrFail($id);
        return response()->json($servicio);
    }

    //  Crear un nuevo servicio
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|max:255',
        ]);

        $servicio = Servicio::create($validated);
        return response()->json($servicio, 201);
    }

    //  Actualizar un servicio existente
    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'sometimes|string|max:255',
        ]);

        $servicio->update($validated);
        return response()->json($servicio);
    }

    //  Eliminar un servicio
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        return response()->json(['mensaje' => 'Servicio eliminado correctamente']);
    }
}

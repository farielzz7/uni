<?php

namespace App\Http\Controllers;

use App\Models\TipoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposServicio = TipoServicio::with('servicio')->get();
        return response()->json($tiposServicio);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_servicio' => 'required|integer|exists:servicios,id',
            'nombre' => 'required|string|unique:tipos_servicio|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipoServicio = TipoServicio::create($request->all());

        return response()->json($tipoServicio, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipoServicio = TipoServicio::with('servicio')->find($id);

        if (is_null($tipoServicio)) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }

        return response()->json($tipoServicio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipoServicio = TipoServicio::find($id);

        if (is_null($tipoServicio)) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_servicio' => 'integer|exists:servicios,id',
            'nombre' => 'string|unique:tipos_servicio,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipoServicio->update($request->all());

        return response()->json($tipoServicio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipoServicio = TipoServicio::find($id);

        if (is_null($tipoServicio)) {
            return response()->json(['message' => 'Tipo de servicio no encontrado'], 404);
        }

        $tipoServicio->delete();

        return response()->json(null, 204);
    }
}

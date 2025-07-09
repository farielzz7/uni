<?php

namespace App\Http\Controllers;

use App\Models\TipoPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoPaqueteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposPaquete = TipoPaquete::all();
        return response()->json($tiposPaquete);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:tipos_paquete|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipoPaquete = TipoPaquete::create($request->all());

        return response()->json($tipoPaquete, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipoPaquete = TipoPaquete::find($id);

        if (is_null($tipoPaquete)) {
            return response()->json(['message' => 'Tipo de paquete no encontrado'], 404);
        }

        return response()->json($tipoPaquete);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipoPaquete = TipoPaquete::find($id);

        if (is_null($tipoPaquete)) {
            return response()->json(['message' => 'Tipo de paquete no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|unique:tipos_paquete,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipoPaquete->update($request->all());

        return response()->json($tipoPaquete);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipoPaquete = TipoPaquete::find($id);

        if (is_null($tipoPaquete)) {
            return response()->json(['message' => 'Tipo de paquete no encontrado'], 404);
        }

        $tipoPaquete->delete();

        return response()->json(null, 204);
    }
}

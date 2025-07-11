<?php

namespace App\Http\Controllers;

use App\Models\TipoPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TipoPaqueteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tiposPaquete = TipoPaquete::with('paquetes')->get(); // Asumiendo relación con Paquetes
        return response()->json($tiposPaquete);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:tipos_paquete|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tipoPaquete = TipoPaquete::create($validator->validated());

        return response()->json($tipoPaquete, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $tipoPaquete = TipoPaquete::with('paquetes')->find($id); // Asumiendo relación con Paquetes

        if (is_null($tipoPaquete)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de paquete no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($tipoPaquete);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tipoPaquete = TipoPaquete::find($id);

        if (is_null($tipoPaquete)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de paquete no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|unique:tipos_paquete,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tipoPaquete->update($validator->validated());

        return response()->json($tipoPaquete);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $tipoPaquete = TipoPaquete::find($id);

        if (is_null($tipoPaquete)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de paquete no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $tipoPaquete->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

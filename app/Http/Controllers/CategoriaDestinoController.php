<?php

namespace App\Http\Controllers;

use App\Models\CategoriaDestino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoriaDestinoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categorias = CategoriaDestino::all();
        return response()->json($categorias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:categorias_destino|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $categoria = CategoriaDestino::create($validator->validated());

        return response()->json($categoria, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $categoria = CategoriaDestino::find($id);

        if (is_null($categoria)) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($categoria);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $categoria = CategoriaDestino::find($id);

        if (is_null($categoria)) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|unique:categorias_destino,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $categoria->update($validator->validated());

        return response()->json($categoria);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $categoria = CategoriaDestino::find($id);

        if (is_null($categoria)) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $categoria->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

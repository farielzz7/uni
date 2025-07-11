<?php

namespace App\Http\Controllers;

use App\Models\TipoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TipoServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tiposServicio = TipoServicio::with('servicio')->get();
        return response()->json($tiposServicio);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_servicio' => 'required|integer|exists:servicios,id',
            'nombre' => 'required|string|unique:tipos_servicio|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tipoServicio = TipoServicio::create($validator->validated());

        return response()->json($tipoServicio, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $tipoServicio = TipoServicio::with('servicio')->find($id);

        if (is_null($tipoServicio)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de servicio no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($tipoServicio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tipoServicio = TipoServicio::find($id);

        if (is_null($tipoServicio)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de servicio no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'id_servicio' => 'sometimes|required|integer|exists:servicios,id',
            'nombre' => 'sometimes|required|string|unique:tipos_servicio,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tipoServicio->update($validator->validated());

        return response()->json($tipoServicio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $tipoServicio = TipoServicio::find($id);

        if (is_null($tipoServicio)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de servicio no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $tipoServicio->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

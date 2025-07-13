<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PaqueteController extends Controller
{
    public function index(): JsonResponse
    {
        $paquetes = Paquete::with(['imagenes', 'servicios', 'destino', 'tipoPaquete'])->get();
        return response()->json($paquetes);
    }

    public function show($id): JsonResponse
    {
        $paquete = Paquete::with(['imagenes', 'servicios', 'destino', 'tipoPaquete'])->findOrFail($id);
        return response()->json($paquete);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'precio' => 'required|numeric|min:0',
                'duracion_dias' => 'required|integer|min:1',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'id_tipo_paquete' => 'required|exists:tipos_paquete,id',
                'id_destino' => 'required|exists:destinos,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $paquete = Paquete::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Paquete creado exitosamente',
                'data' => $paquete
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al crear el paquete: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $paquete = Paquete::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'sometimes|required|string',
                'precio' => 'sometimes|required|numeric|min:0',
                'duracion_dias' => 'sometimes|required|integer|min:1',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
                'id_tipo_paquete' => 'sometimes|required|exists:tipos_paquete,id',
                'id_destino' => 'sometimes|required|exists:destinos,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $paquete->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Paquete actualizado exitosamente',
                'data' => $paquete
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al actualizar el paquete: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $paquete = Paquete::findOrFail($id);
            $paquete->delete();

            return response()->json([
                'success' => true,
                'message' => 'Paquete eliminado exitosamente'
            ], Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al eliminar el paquete: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
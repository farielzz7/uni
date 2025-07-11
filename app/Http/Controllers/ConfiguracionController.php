<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConfiguracionController extends Controller
{
    public function index(): JsonResponse
    {
        $configuraciones = Configuracion::all();
        return response()->json($configuraciones);
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'clave' => 'required|string|max:255',
                'valor' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $configuracion = Configuracion::updateOrCreate(
                ['clave' => $request->clave],
                ['valor' => $request->valor]
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente',
                'data' => $configuracion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al actualizar la configuración: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

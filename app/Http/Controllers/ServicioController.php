<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServicioController extends Controller
{
    //  Listar servicios
    public function index(): JsonResponse
    {
        $servicios = Servicio::with(['tiposServicio', 'proveedores', 'paquetes'])->get();
        return response()->json($servicios);
    }

    //  Mostrar servicio específico
    public function show($id): JsonResponse
    {
        $servicio = Servicio::with(['tiposServicio', 'proveedores', 'paquetes'])->find($id);

        if (is_null($servicio)) {
            return response()->json([
                'success' => false,
                'message' => 'Servicio no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($servicio);
    }

    //  Crear un nuevo servicio
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $servicio = Servicio::create($validator->validated());
        return response()->json($servicio, Response::HTTP_CREATED);
    }

    //  Actualizar un servicio existente
    public function update(Request $request, $id): JsonResponse
    {
        $servicio = Servicio::find($id);

        if (is_null($servicio)) {
            return response()->json([
                'success' => false,
                'message' => 'Servicio no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $servicio->update($validator->validated());
        return response()->json($servicio);
    }

    //  Eliminar un servicio
    public function destroy($id): JsonResponse
    {
        $servicio = Servicio::find($id);

        if (is_null($servicio)) {
            return response()->json([
                'success' => false,
                'message' => 'Servicio no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $servicio->delete();

        return response()->json(['success' => true, 'message' => 'Servicio eliminado correctamente'], Response::HTTP_NO_CONTENT);
    }
}

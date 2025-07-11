<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DestinoController extends Controller
{
    /**
     * Muestra una lista paginada de destinos.
     * Permite una búsqueda básica por nombre.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Destino::query();

        // Búsqueda simple por nombre
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Carga la imagen principal para un rendimiento óptimo en la lista
        $destinos = $query->with('imagenes')->paginate(15);

        return response()->json($destinos);
    }

    /**
     * Crea un nuevo destino en la base de datos.
     * Acción restringida a administradores.
     */
    public function store(Request $request): JsonResponse
    {
        // A futuro, aquí se debería verificar la autorización:
        // $this->authorize('create', Destino::class);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:destinos|max:255',
            'descripcion' => 'required|string',
            'eventos' => 'nullable|string',
            'atractivos' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $destino = Destino::create($validator->validated());

        return response()->json($destino, Response::HTTP_CREATED);
    }

    /**
     * Muestra un destino específico con todas sus relaciones.
     */
    public function show(Destino $destino): JsonResponse
    {
        // Carga eficiente de todas las relaciones para la vista de detalle
        $destino->load(['categorias', 'imagenes', 'comentarios.turista', 'hoteles']);
        return response()->json($destino);
    }

    /**
     * Actualiza un destino existente.
     * Acción restringida a administradores.
     */
    public function update(Request $request, Destino $destino): JsonResponse
    {
        // A futuro, aquí se debería verificar la autorización:
        // $this->authorize('update', $destino);

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255|unique:destinos,nombre,' . $destino->id,
            'descripcion' => 'sometimes|required|string',
            'eventos' => 'nullable|string',
            'atractivos' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $destino->update($validator->validated());

        return response()->json($destino);
    }

    /**
     * Elimina un destino.
     * Acción restringida a administradores.
     */
    public function destroy(Destino $destino): JsonResponse
    {
        // A futuro, aquí se debería verificar la autorización:
        // $this->authorize('delete', $destino);

        $destino->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

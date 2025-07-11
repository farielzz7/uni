<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Turista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ComentarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(): JsonResponse
    {
        $comentarios = Comentario::with('turista', 'destino')
            ->orderBy('fecha', 'desc')
            ->get();
        return response()->json($comentarios);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'texto' => 'required|string|max:1000',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'id_destino' => 'required|exists:destinos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $turista = Turista::where('id_usuario', Auth::id())
            ->firstOrFail();

        $comentario = Comentario::create([
            'id_turista' => $turista->id,
            'id_destino' => $request->id_destino,
            'texto' => $request->texto,
            'calificacion' => $request->calificacion,
            'fecha' => now()
        ]);

        return response()->json($comentario->load('turista', 'destino'), Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $comentario = Comentario::with('turista', 'destino')
            ->find($id);

        if (is_null($comentario)) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($comentario);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $comentario = Comentario::find($id);

        if (is_null($comentario)) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el autor del comentario puede actualizarlo
        $turista = Turista::where('id_usuario', Auth::id())->first();
        if (!$turista || $comentario->id_turista !== $turista->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para actualizar este comentario'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'texto' => 'sometimes|required|string|max:1000',
            'calificacion' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $comentario->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Comentario actualizado exitosamente.',
            'comentario' => $comentario->load('turista', 'destino')
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $comentario = Comentario::find($id);

        if (is_null($comentario)) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el autor del comentario puede eliminarlo
        $turista = Turista::where('id_usuario', Auth::id())->first();
        if (!$turista || $comentario->id_turista !== $turista->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para eliminar este comentario'
            ], Response::HTTP_FORBIDDEN);
        }

        $comentario->delete();

        return response()->json(['success' => true, 'message' => 'Comentario eliminado exitosamente.'], Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SuscripcionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = $user->suscripciones(); // Asumiendo que el modelo User tiene una relación hasMany con Suscripcion

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $suscripciones = $query->with('usuario')->get();
        return response()->json($suscripciones);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_suscripcion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string|in:activa,inactiva,cancelada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $data['id_usuario'] = Auth::id();
        $data['estado'] = $data['estado'] ?? 'activa';

        $suscripcion = Suscripcion::create($data);

        return response()->json($suscripcion->load('usuario'), Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $suscripcion = $user->suscripciones()->with('usuario')->find($id);

        if (is_null($suscripcion)) {
            return response()->json([
                'success' => false,
                'message' => 'Suscripción no encontrada o no pertenece al usuario autenticado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($suscripcion);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $suscripcion = Suscripcion::find($id);

        if (is_null($suscripcion)) {
            return response()->json([
                'success' => false,
                'message' => 'Suscripción no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario de la suscripción puede actualizarla
        if (Auth::id() !== $suscripcion->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para actualizar esta suscripción'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'tipo_suscripcion' => 'sometimes|required|string|max:255',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'sometimes|required|string|in:activa,inactiva,cancelada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $suscripcion->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Suscripción actualizada exitosamente.',
            'suscripcion' => $suscripcion->load('usuario')
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $suscripcion = Suscripcion::find($id);

        if (is_null($suscripcion)) {
            return response()->json([
                'success' => false,
                'message' => 'Suscripción no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario de la suscripción puede eliminarla
        if (Auth::id() !== $suscripcion->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para eliminar esta suscripción'
            ], Response::HTTP_FORBIDDEN);
        }

        $suscripcion->delete();

        return response()->json(['success' => true, 'message' => 'Suscripción eliminada exitosamente.'], Response::HTTP_NO_CONTENT);
    }
}

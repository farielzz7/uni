<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Turista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ReservaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $turista = $user->turista; // Asumiendo que el usuario tiene un perfil de turista

        if (is_null($turista)) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de turista no encontrado para el usuario autenticado'
            ], Response::HTTP_NOT_FOUND);
        }

        $reservas = $turista->reservas()->with(['hotel'])->get();
        return response()->json($reservas);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_hotel' => 'required|exists:hoteles,id',
            'fecha_entrada' => 'required|date|after_or_equal:today',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'numero_personas' => 'required|integer|min:1',
            'estado' => 'nullable|string|in:pendiente,confirmada,cancelada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Auth::user();
        $turista = $user->turista;

        if (is_null($turista)) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de turista no encontrado para el usuario autenticado'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $validator->validated();
        $data['id_turista'] = $turista->id;
        $data['estado'] = $data['estado'] ?? 'pendiente';

        $reserva = Reserva::create($data);

        return response()->json($reserva->load(['hotel']), Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $turista = $user->turista;

        if (is_null($turista)) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de turista no encontrado para el usuario autenticado'
            ], Response::HTTP_NOT_FOUND);
        }

        $reserva = $turista->reservas()->with(['hotel'])->find($id);

        if (is_null($reserva)) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada o no pertenece al usuario autenticado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($reserva);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $reserva = Reserva::find($id);

        if (is_null($reserva)) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario de la reserva puede actualizarla
        $user = Auth::user();
        $turista = $user->turista;

        if (is_null($turista) || $reserva->id_turista !== $turista->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para actualizar esta reserva'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'fecha_entrada' => 'sometimes|required|date|after_or_equal:today',
            'fecha_salida' => 'sometimes|required|date|after:fecha_entrada',
            'numero_personas' => 'sometimes|required|integer|min:1',
            'estado' => 'sometimes|required|string|in:pendiente,confirmada,cancelada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $reserva->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada exitosamente.',
            'reserva' => $reserva->load(['hotel'])
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $reserva = Reserva::find($id);

        if (is_null($reserva)) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario de la reserva puede eliminarla
        $user = Auth::user();
        $turista = $user->turista;

        if (is_null($turista) || $reserva->id_turista !== $turista->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para eliminar esta reserva'
            ], Response::HTTP_FORBIDDEN);
        }

        $reserva->delete();

        return response()->json(['success' => true, 'message' => 'Reserva eliminada exitosamente.'], Response::HTTP_NO_CONTENT);
    }
}

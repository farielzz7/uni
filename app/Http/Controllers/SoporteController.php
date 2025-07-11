<?php

namespace App\Http\Controllers;

use App\Models\Soporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SoporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Soporte::query();

        // Si el usuario no es administrador, solo puede ver sus propias solicitudes
        // Asumiendo que el modelo User tiene un método hasRole
        // if (!$user->hasRole('admin')) {
        //     $query->where('id_usuario', $user->id);
        // }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $soportes = $query->with('user')->get();
        return response()->json($soportes);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'estado' => 'nullable|string|in:abierto,en_progreso,cerrado',
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
        $data['fecha_creacion'] = now();
        $data['estado'] = $data['estado'] ?? 'abierto';

        $soporte = Soporte::create($data);

        return response()->json($soporte->load('user'), Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $soporte = Soporte::with('user')->find($id);

        if (is_null($soporte)) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitud de soporte no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario o un administrador pueden ver la solicitud
        // if (Auth::id() !== $soporte->id_usuario && (!Auth::user() || !Auth::user()->hasRole('admin'))) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No autorizado para ver esta solicitud de soporte'
        //     ], Response::HTTP_FORBIDDEN);
        // }

        return response()->json($soporte);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $soporte = Soporte::find($id);

        if (is_null($soporte)) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitud de soporte no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario o un administrador pueden actualizar la solicitud
        // if (Auth::id() !== $soporte->id_usuario && (!Auth::user() || !Auth::user()->hasRole('admin'))) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No autorizado para actualizar esta solicitud de soporte'
        //     ], Response::HTTP_FORBIDDEN);
        // }

        $validator = Validator::make($request->all(), [
            'asunto' => 'sometimes|required|string|max:255',
            'mensaje' => 'sometimes|required|string',
            'estado' => 'sometimes|required|string|in:abierto,en_progreso,cerrado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $soporte->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de soporte actualizada exitosamente.',
            'soporte' => $soporte->load('user')
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $soporte = Soporte::find($id);

        if (is_null($soporte)) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitud de soporte no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario o un administrador pueden eliminar la solicitud
        // if (Auth::id() !== $soporte->id_usuario && (!Auth::user() || !Auth::user()->hasRole('admin'))) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No autorizado para eliminar esta solicitud de soporte'
        //     ], Response::HTTP_FORBIDDEN);
        // }

        $soporte->delete();

        return response()->json(['success' => true, 'message' => 'Solicitud de soporte eliminada exitosamente.'], Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = $user->notificaciones(); // Asumiendo que el modelo User tiene una relación hasMany con Notificacion

        if ($request->has('leido')) {
            $query->where('leido', $request->leido);
        }

        $notificaciones = $query->paginate(15);

        return response()->json($notificaciones);
    }

    public function markAsRead($id): JsonResponse
    {
        $notificacion = Notificacion::find($id);

        if (is_null($notificacion)) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Autorización: Solo el propietario de la notificación puede marcarla como leída
        if (Auth::id() !== $notificacion->id_usuario) { // Asumiendo que Notificacion tiene id_usuario
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para marcar esta notificación como leída'
            ], Response::HTTP_FORBIDDEN);
        }

        $notificacion->update(['leido' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ], Response::HTTP_OK);
    }
}

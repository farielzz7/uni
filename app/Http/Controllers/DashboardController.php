<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();

        // Ejemplo: Obtener las reservas del usuario autenticado
        // Asumiendo que el modelo User tiene una relación hasMany con Reserva
        // y que Reserva tiene una relación belongsTo con Turista, y Turista con User
        $reservas = [];
        if ($user->turista) {
            $reservas = $user->turista->reservas()->with('paquete')->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Bienvenido al dashboard',
            'data' => [
                'user' => $user->only(['id', 'email']),
                'reservas_proximas' => $reservas,
                // Aquí se pueden añadir más datos relevantes para el dashboard
            ]
        ], Response::HTTP_OK);
    }
}

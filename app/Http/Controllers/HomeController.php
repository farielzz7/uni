<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $paquetesDestacados = Paquete::with(['imagenes', 'servicios', 'destino', 'tipoPaquete'])
                                    ->where('destacado', true)
                                    ->get();

        // Aquí podrías añadir lógica para obtener la descripción de la empresa, etc.
        // Por ahora, asumimos que es contenido estático en el frontend o se obtendrá de otro endpoint.

        return response()->json([
            'success' => true,
            'message' => 'Datos de la página de inicio obtenidos exitosamente',
            'data' => [
                'paquetes_destacados' => $paquetesDestacados,
                // 'descripcion_empresa' => 'Somos GoPlan, tu agencia de viajes de confianza...',
                // 'introduccion' => 'Descubre el mundo con nosotros...',
            ]
        ], Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ItinerarioPersonalizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ItinerarioPersonalizadoController extends Controller
{
    public function index(): JsonResponse
    {
        $itinerarios = ItinerarioPersonalizado::with(['user', 'paquete'])->get();
        return response()->json($itinerarios);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:users,id',
            'id_paquete' => 'nullable|exists:paquetes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'presupuesto_total' => 'required|numeric|min:0',
            'detalles_json' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $itinerario = ItinerarioPersonalizado::create($validator->validated());
        return response()->json($itinerario, Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $itinerario = ItinerarioPersonalizado::with(['user', 'paquete'])->find($id);

        if (is_null($itinerario)) {
            return response()->json([
                'success' => false,
                'message' => 'Itinerario personalizado no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($itinerario);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $itinerario = ItinerarioPersonalizado::find($id);

        if (is_null($itinerario)) {
            return response()->json([
                'success' => false,
                'message' => 'Itinerario personalizado no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'id_paquete' => 'nullable|exists:paquetes,id',
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'presupuesto_total' => 'sometimes|required|numeric|min:0',
            'detalles_json' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $itinerario->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Itinerario personalizado actualizado exitosamente.',
            'itinerario' => $itinerario
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $itinerario = ItinerarioPersonalizado::find($id);

        if (is_null($itinerario)) {
            return response()->json([
                'success' => false,
                'message' => 'Itinerario personalizado no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $itinerario->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function generatePersonalizedItinerary(Request $request): JsonResponse
    {
        // Validar los parámetros de entrada para la generación del itinerario
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:users,id',
            'presupuesto' => 'required|numeric|min:0',
            'preferencias' => 'nullable|array', // Ej. ['aventura', 'relajacion']
            'destinos_preferidos' => 'nullable|array',
            'fechas_viaje' => 'nullable|array', // Ej. ['inicio' => 'YYYY-MM-DD', 'fin' => 'YYYY-MM-DD']
            // ... otros parámetros relevantes para la personalización
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        // --- Lógica compleja para la generación del itinerario personalizado ---
        // 1. Realizar llamadas a APIs externas (Amadeus, Booking, TripAdvisor, etc.)
        //    basándose en el presupuesto, preferencias, destinos, fechas, etc.
        //    Ejemplos de APIs en .env: AMADEUS_CLIENT_ID, BOOKING_API_KEY, TRIPADVISOR_API_KEY
        // 2. Procesar las respuestas de las APIs para encontrar vuelos, hoteles, actividades, etc.
        // 3. Aplicar la lógica de "ajustables de los porcentajes" para distribuir el presupuesto.
        // 4. Construir la estructura detallada del itinerario (días, actividades, alojamientos, transporte).
        //    Esto se almacenaría en la columna `detalles_json` del modelo ItinerarioPersonalizado.
        // 5. Crear un nuevo registro ItinerarioPersonalizado con los datos generados.

        // Placeholder para el itinerario generado
        $itinerarioGenerado = [
            'nombre' => 'Itinerario Personalizado para ' . $validatedData['id_usuario'],
            'descripcion' => 'Itinerario generado basado en sus preferencias y presupuesto.',
            'presupuesto_total' => $validatedData['presupuesto'],
            'detalles_json' => json_encode([
                'dia_1' => 'Actividad de ejemplo',
                'dia_2' => 'Otra actividad de ejemplo',
            ]),
            'id_usuario' => $validatedData['id_usuario'],
        ];

        $itinerario = ItinerarioPersonalizado::create($itinerarioGenerado);

        return response()->json([
            'success' => true,
            'message' => 'Itinerario personalizado generado exitosamente.',
            'itinerario' => $itinerario->load(['user', 'paquete'])
        ], Response::HTTP_CREATED);
    }
}

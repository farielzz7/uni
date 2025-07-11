<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $hoteles = Hotel::with(['imagenes', 'habitaciones', 'servicios'])->get(); // Asumiendo estas relaciones
        return response()->json($hoteles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estrellas' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hotel = Hotel::create($validator->validated());

        return response()->json($hotel, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $hotel = Hotel::with(['imagenes', 'habitaciones', 'servicios'])->find($id); // Asumiendo estas relaciones

        if (is_null($hotel)) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($hotel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $hotel = Hotel::find($id);

        if (is_null($hotel)) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'ubicacion' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'estrellas' => 'sometimes|required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hotel->update($validator->validated());

        return response()->json($hotel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $hotel = Hotel::find($id);

        if (is_null($hotel)) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $hotel->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

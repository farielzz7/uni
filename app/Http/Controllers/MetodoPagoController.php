<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $metodosPago = MetodoPago::all();
        return response()->json($metodosPago);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:metodos_pago|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metodoPago = MetodoPago::create($validator->validated());

        return response()->json($metodoPago, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $metodoPago = MetodoPago::find($id);

        if (is_null($metodoPago)) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($metodoPago);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $metodoPago = MetodoPago::find($id);

        if (is_null($metodoPago)) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|unique:metodos_pago,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metodoPago->update($validator->validated());

        return response()->json($metodoPago);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $metodoPago = MetodoPago::find($id);

        if (is_null($metodoPago)) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $metodoPago->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

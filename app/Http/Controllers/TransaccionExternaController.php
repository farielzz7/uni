<?php

namespace App\Http\Controllers;

use App\Models\TransaccionExterna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransaccionExternaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $transacciones = TransaccionExterna::with('pago')->get();
        return response()->json($transacciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_pago' => 'required|integer|exists:pagos,id',
            'proveedor' => 'required|string|max:255',
            'respuesta_raw' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $transaccion = TransaccionExterna::create($validator->validated());

        return response()->json($transaccion, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $transaccion = TransaccionExterna::with('pago')->find($id);

        if (is_null($transaccion)) {
            return response()->json([
                'success' => false,
                'message' => 'Transacción externa no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($transaccion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $transaccion = TransaccionExterna::find($id);

        if (is_null($transaccion)) {
            return response()->json([
                'success' => false,
                'message' => 'Transacción externa no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'id_pago' => 'sometimes|required|integer|exists:pagos,id',
            'proveedor' => 'sometimes|required|string|max:255',
            'respuesta_raw' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $transaccion->update($validator->validated());

        return response()->json($transaccion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $transaccion = TransaccionExterna::find($id);

        if (is_null($transaccion)) {
            return response()->json([
                'success' => false,
                'message' => 'Transacción externa no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $transaccion->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

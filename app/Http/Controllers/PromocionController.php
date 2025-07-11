<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PromocionController extends Controller
{
    public function index(): JsonResponse
    {
        $promociones = Promocion::all();
        return response()->json($promociones);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|unique:promociones|max:255',
            'tipo_descuento' => 'required|in:porcentaje,fijo',
            'valor_descuento' => 'required|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $promocion = Promocion::create($validator->validated());

        return response()->json($promocion, Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $promocion = Promocion::find($id);

        if (is_null($promocion)) {
            return response()->json([
                'success' => false,
                'message' => 'Promoción no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($promocion);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $promocion = Promocion::find($id);

        if (is_null($promocion)) {
            return response()->json([
                'success' => false,
                'message' => 'Promoción no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'codigo' => 'sometimes|required|string|unique:promociones,codigo,' . $id . '|max:255',
            'tipo_descuento' => 'sometimes|required|in:porcentaje,fijo',
            'valor_descuento' => 'sometimes|required|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $promocion->update($validator->validated());

        return response()->json($promocion);
    }

    public function destroy($id): JsonResponse
    {
        $promocion = Promocion::find($id);

        if (is_null($promocion)) {
            return response()->json([
                'success' => false,
                'message' => 'Promoción no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $promocion->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function aplicarDescuento(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string',
            'monto' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $codigo = $request->input('codigo');
        $monto = $request->input('monto');

        $promocion = Promocion::where('codigo', $codigo)
            ->where('activo', true)
            ->where(function ($query) {
                $query->whereNull('fecha_inicio')
                      ->orWhere('fecha_inicio', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>=', now());
            })
            ->first();

        if ($promocion) {
            $montoFinal = $monto;
            if ($promocion->tipo_descuento === 'porcentaje') {
                $montoFinal = $monto - ($monto * ($promocion->valor_descuento / 100));
            } elseif ($promocion->tipo_descuento === 'fijo') {
                $montoFinal = $monto - $promocion->valor_descuento;
            }

            // Asegurarse de que el monto final no sea negativo
            $montoFinal = max(0, $montoFinal);

            return response()->json([
                'success' => true,
                'message' => 'Descuento aplicado correctamente',
                'monto_original' => $monto,
                'monto_final' => $montoFinal,
                'descuento_aplicado' => $monto - $montoFinal,
                'promocion' => $promocion
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Código de promoción no válido o inactivo'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}

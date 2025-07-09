<?php

namespace App\Http\Controllers;

use App\Models\TransaccionExterna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaccionExternaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transacciones = TransaccionExterna::with('pago')->get();
        return response()->json($transacciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pago' => 'required|integer|exists:pagos,id',
            'proveedor' => 'required|string|max:255',
            'respuesta_raw' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $transaccion = TransaccionExterna::create($request->all());

        return response()->json($transaccion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaccion = TransaccionExterna::with('pago')->find($id);

        if (is_null($transaccion)) {
            return response()->json(['message' => 'Transacción externa no encontrada'], 404);
        }

        return response()->json($transaccion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaccion = TransaccionExterna::find($id);

        if (is_null($transaccion)) {
            return response()->json(['message' => 'Transacción externa no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_pago' => 'integer|exists:pagos,id',
            'proveedor' => 'string|max:255',
            'respuesta_raw' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $transaccion->update($request->all());

        return response()->json($transaccion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaccion = TransaccionExterna::find($id);

        if (is_null($transaccion)) {
            return response()->json(['message' => 'Transacción externa no encontrada'], 404);
        }

        $transaccion->delete();

        return response()->json(null, 204);
    }
}

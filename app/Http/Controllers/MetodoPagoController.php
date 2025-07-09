<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $metodosPago = MetodoPago::all();
        return response()->json($metodosPago);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:metodos_pago|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $metodoPago = MetodoPago::create($request->all());

        return response()->json($metodoPago, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $metodoPago = MetodoPago::find($id);

        if (is_null($metodoPago)) {
            return response()->json(['message' => 'Método de pago no encontrado'], 404);
        }

        return response()->json($metodoPago);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $metodoPago = MetodoPago::find($id);

        if (is_null($metodoPago)) {
            return response()->json(['message' => 'Método de pago no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|unique:metodos_pago,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $metodoPago->update($request->all());

        return response()->json($metodoPago);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $metodoPago = MetodoPago::find($id);

        if (is_null($metodoPago)) {
            return response()->json(['message' => 'Método de pago no encontrado'], 404);
        }

        $metodoPago->delete();

        return response()->json(null, 204);
    }
}

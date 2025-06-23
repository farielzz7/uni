<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::all();
        return response()->json($promociones);
    }

    public function aplicarDescuento(Request $request)
    {
        $codigo = $request->input('codigo');
        $promocion = Promocion::where('codigo', $codigo)->first();

        if ($promocion) {
            // Aplicar descuento
            return response()->json(['message' => 'Descuento aplicado correctamente']);
        } else {
            return response()->json(['error' => 'Código de promoción no válido'], 404);
        }
    }
}

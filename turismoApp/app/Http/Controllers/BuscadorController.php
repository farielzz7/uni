<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;

class BuscadorController extends Controller
{
    public function index()
    {
        $paquetes = Paquete::all();
        return response()->json($paquetes);
    }

    public function buscar(Request $request)
    {
        $presupuesto = $request->input('presupuesto');
        $paquetes = Paquete::where('precio', '<=', $presupuesto)->get();
        return response()->json($paquetes);
    }

    public function personalizar(Request $request)
    {
        $paquete = Paquete::findOrFail($request->input('id_paquete'));
        return response()->json($paquete);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::all();
        return response()->json($facturas);
    }

    public function show($id)
    {
        $factura = Factura::findOrFail($id);
        return response()->json($factura);
    }
}

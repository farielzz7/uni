<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function index()
    {
        $suscripciones = Suscripcion::all();
        return response()->json($suscripciones);
    }

    public function store(Request $request)
    {
        $suscripcion = Suscripcion::create($request->all());
        return response()->json($suscripcion, 201);
    }

    public function show($id)
    {
        $suscripcion = Suscripcion::findOrFail($id);
        return response()->json($suscripcion);
    }
}

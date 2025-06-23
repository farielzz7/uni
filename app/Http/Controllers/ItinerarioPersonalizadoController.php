<?php

namespace App\Http\Controllers;

use App\Models\ItinerarioPersonalizado;
use Illuminate\Http\Request;

class ItinerarioPersonalizadoController extends Controller
{
    public function index()
    {
        $itinerarios = ItinerarioPersonalizado::all();
        return response()->json($itinerarios);
    }

    public function store(Request $request)
    {
        $itinerario = ItinerarioPersonalizado::create($request->all());
        return response()->json($itinerario, 201);
    }

    public function show($id)
    {
        $itinerario = ItinerarioPersonalizado::findOrFail($id);
        return response()->json($itinerario);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Itinerario;
use Illuminate\Http\Request;

class ItinerarioController extends Controller
{
    public function index()
    {
        $itinerarios = Itinerario::all();
        return response()->json($itinerarios);
    }

    public function show($id)
    {
        $itinerario = Itinerario::findOrFail($id);
        return response()->json($itinerario);
    }
}

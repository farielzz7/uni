<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::all();
        return response()->json($reservas);
    }

    public function store(Request $request)
    {
        $reserva = Reserva::create($request->all());
        return response()->json($reserva, 201);
    }

    public function show($id)
    {
        $reserva = Reserva::findOrFail($id);
        return response()->json($reserva);
    }
}

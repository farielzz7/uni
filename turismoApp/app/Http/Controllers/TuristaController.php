<?php

namespace App\Http\Controllers;

use App\Models\Turista;
use Illuminate\Http\Request;

class TuristaController extends Controller
{
    public function index()
    {
        $turistas = Turista::all();
        return response()->json($turistas);
    }

    public function show($id)
    {
        $turista = Turista::findOrFail($id);
        return response()->json($turista);
    }

    public function update(Request $request, $id)
    {
        $turista = Turista::findOrFail($id);
        $turista->update($request->all());
        return response()->json($turista);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Soporte;
use Illuminate\Http\Request;

class SoporteController extends Controller
{
    public function index()
    {
        $soportes = Soporte::all();
        return response()->json($soportes);
    }

    public function store(Request $request)
    {
        $soporte = Soporte::create($request->all());
        return response()->json($soporte, 201);
    }

    public function show($id)
    {
        $soporte = Soporte::findOrFail($id);
        return response()->json($soporte);
    }
}

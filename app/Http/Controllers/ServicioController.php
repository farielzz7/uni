<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        return response()->json($servicios);
    }

    public function show($id)
    {
        $servicio = Servicio::findOrFail($id);
        return response()->json($servicio);
    }
}

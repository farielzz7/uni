<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;

class PaqueteController extends Controller
{
    public function index()
    {
        $paquetes = Paquete::all();
        return response()->json($paquetes);
    }

    public function show($id)
    {
        $paquete = Paquete::findOrFail($id);
        return response()->json($paquete);
    }
}

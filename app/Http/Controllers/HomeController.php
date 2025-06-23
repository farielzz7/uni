<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $paquetesDestacados = Paquete::where('destacado', true)->get();
        return response()->json($paquetesDestacados);
    }
}

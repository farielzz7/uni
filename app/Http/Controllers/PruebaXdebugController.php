<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebaXdebugController extends Controller
{
    public function index()
    {
        $saludo = "Hola desde Xdebug"; // ← AQUÍ pondrás el breakpoint
        return response()->json([
            'mensaje' => $saludo
        ]);
    }
}

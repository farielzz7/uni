<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function update(Request $request)
    {
        // Lógica para actualizar la configuración
        return response()->json(['message' => 'Configuración actualizada correctamente']);
    }
}

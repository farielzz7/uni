<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Lógica para enviar el mensaje de contacto
        return response()->json(['message' => 'Mensaje enviado correctamente']);
    }
}

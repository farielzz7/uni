<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RedSocialController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function seguirUsuario(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Lógica para seguir al usuario
        return response()->json(['message' => 'Usuario seguido correctamente']);
    }
}

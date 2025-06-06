<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function index()
    {
        $comentarios = Comentario::all();
        return response()->json($comentarios);
    }

    public function store(Request $request)
    {
        $comentario = Comentario::create($request->all());
        return response()->json($comentario, 201);
    }

    public function show($id)
    {
        $comentario = Comentario::findOrFail($id);
        return response()->json($comentario);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Turista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComentarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index()
    {
        $comentarios = Comentario::with('turista', 'destino')
            ->orderBy('fecha', 'desc')
            ->get();
        return response()->json($comentarios);
    }

    public function store(Request $request)
    {
        $request->validate([
            'texto' => 'required|string|max:1000',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'id_destino' => 'required|exists:destinos,id'
        ]);

        $turista = Turista::where('id_usuario', Auth::id())
            ->firstOrFail();

        $comentario = Comentario::create([
            'id_turista' => $turista->id,
            'id_destino' => $request->id_destino,
            'texto' => $request->texto,
            'calificacion' => $request->calificacion,
            'fecha' => now()
        ]);

        return response()->json($comentario->load('turista', 'destino'), 201);
    }

    public function show($id)
    {
        $comentario = Comentario::with('turista', 'destino')
            ->findOrFail($id);
        return response()->json($comentario);
    }
}

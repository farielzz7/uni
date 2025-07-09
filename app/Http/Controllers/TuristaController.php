<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TuristaController extends Controller
{
    public function index()
    {
    $turistas = Turista::with(['user', 'reservas', 'pagos', 'comentarios'])->get();

    return response()->json($turistas);
    }

    // Registro de turista (usuarios + perfil)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Datos de usuario
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',

            // Datos de turista
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'nacionalidad' => 'required|string|max:50',
            'edad' => 'required|integer|min:0',
            'telefono' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear usuario
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Crear perfil de turista
        $turista = Turista::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'nacionalidad' => $request->nacionalidad,
            'edad' => $request->edad,
            'telefono' => $request->telefono,
            'id_usuario' => $user->id,
        ]);

        return response()->json([
            'message' => 'Turista registrado correctamente.',
            'user' => $user,
            'turista' => $turista
        ], 201);
    }

    // Mostrar perfil de un turista
    public function show($id)
    {
        $turista = Turista::with('user')->findOrFail($id);
        return response()->json($turista);
    }

    // Actualizar perfil de turista
    public function update(Request $request, $id)
    {
        $turista = Turista::findOrFail($id);

        $turista->update($request->only([
            'nombre', 'apellido', 'nacionalidad', 'edad', 'telefono'
        ]));

        return response()->json([
            'message' => 'Turista actualizado correctamente.',
            'turista' => $turista
        ]);
    }

    //  Eliminar turista (y opcionalmente el usuario)
    public function destroy($id)
    {
        $turista = Turista::findOrFail($id);
        $user = $turista->user;

        $turista->delete();
        if ($user) {
            $user->delete();
        }

        return response()->json(['message' => 'Turista y usuario eliminados correctamente.']);
    }
}

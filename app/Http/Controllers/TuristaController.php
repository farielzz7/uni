<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TuristaController extends Controller
{
    public function index(): JsonResponse
    {
        $turistas = Turista::with(['user', 'reservas', 'pagos', 'comentarios'])->get();
        return response()->json($turistas);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'nacionalidad' => 'required|string|max:50',
            'edad' => 'required|integer|min:0',
            'telefono' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData = $validator->validated();

        $user = User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $turista = Turista::create([
            'nombre' => $validatedData['nombre'],
            'apellido' => $validatedData['apellido'],
            'nacionalidad' => $validatedData['nacionalidad'],
            'edad' => $validatedData['edad'],
            'telefono' => $validatedData['telefono'],
            'id_usuario' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Turista registrado correctamente.',
            'user' => $user,
            'turista' => $turista
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $turista = Turista::with('user')->find($id);

        if (is_null($turista)) {
            return response()->json([
                'success' => false,
                'message' => 'Turista no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($turista);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $turista = Turista::find($id);

        if (is_null($turista)) {
            return response()->json([
                'success' => false,
                'message' => 'Turista no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'nacionalidad' => 'sometimes|required|string|max:50',
            'edad' => 'sometimes|required|integer|min:0',
            'telefono' => 'sometimes|required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $turista->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Turista actualizado correctamente.',
            'turista' => $turista
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $turista = Turista::find($id);

        if (is_null($turista)) {
            return response()->json([
                'success' => false,
                'message' => 'Turista no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $user = $turista->user;

        $turista->delete();
        if ($user) {
            $user->delete();
        }

        return response()->json(['success' => true, 'message' => 'Turista y usuario eliminados correctamente.'], Response::HTTP_NO_CONTENT);
    }
}

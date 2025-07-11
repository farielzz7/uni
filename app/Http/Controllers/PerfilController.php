<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PerfilController extends Controller
{
    public function show($id): JsonResponse
    {
        $user = User::with(['turista', 'reservas', 'comentarios', 'pagos'])->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Autorización: Solo el usuario autenticado puede actualizar su propio perfil
            if (Auth::id() != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para actualizar este perfil'
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|min:8|confirmed',
                'nombre' => 'sometimes|required|string|min:2|max:50',
                'apellido' => 'sometimes|required|string|min:2|max:50',
                'nacionalidad' => 'sometimes|required|string|max:50',
                'edad' => 'sometimes|required|integer|min:18|max:100',
                'telefono' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Actualizar datos del usuario
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Actualizar datos del turista asociado
            if ($user->turista) {
                $turistaData = $request->only(['nombre', 'apellido', 'nacionalidad', 'edad', 'telefono']);
                $user->turista->update($turistaData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => $user->load('turista') // Recargar para mostrar los datos actualizados del turista
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al actualizar el perfil: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

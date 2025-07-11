<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserRoleController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar roles de usuario
        // $this->middleware('auth:sanctum');
        // $this->middleware('role:admin'); // Asumiendo un middleware de roles
    }

    public function getUserRoles($userId): JsonResponse
    {
        $user = User::with('roles')->find($userId);

        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'user' => $user->only('id', 'email'),
            'roles' => $user->roles
        ]);
    }

    public function assignRole(Request $request, $userId): JsonResponse
    {
        $user = User::find($userId);

        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->roles()->syncWithoutDetaching($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'Roles asignados exitosamente',
            'user' => $user->load('roles')
        ]);
    }

    public function revokeRole(Request $request, $userId): JsonResponse
    {
        $user = User::find($userId);

        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->roles()->detach($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'Roles revocados exitosamente',
            'user' => $user->load('roles')
        ]);
    }
}

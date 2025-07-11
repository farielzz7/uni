<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar permisos de roles
        // $this->middleware('auth:sanctum');
        // $this->middleware('role:admin'); // Asumiendo un middleware de roles
    }

    public function getRolePermissions($roleId): JsonResponse
    {
        $rol = Rol::with('permisos')->find($roleId);

        if (is_null($rol)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'rol' => $rol->only('id', 'nombre', 'clave_rol'),
            'permisos' => $rol->permisos
        ]);
    }

    public function assignPermission(Request $request, $roleId): JsonResponse
    {
        $rol = Rol::find($roleId);

        if (is_null($rol)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'permisos' => 'required|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rol->permisos()->syncWithoutDetaching($request->permisos);

        return response()->json([
            'success' => true,
            'message' => 'Permisos asignados exitosamente',
            'rol' => $rol->load('permisos')
        ]);
    }

    public function revokePermission(Request $request, $roleId): JsonResponse
    {
        $rol = Rol::find($roleId);

        if (is_null($rol)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'permisos' => 'required|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rol->permisos()->detach($request->permisos);

        return response()->json([
            'success' => true,
            'message' => 'Permisos revocados exitosamente',
            'rol' => $rol->load('permisos')
        ]);
    }
}

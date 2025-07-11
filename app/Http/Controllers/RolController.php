<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RolController extends Controller
{
    //  Listar todos los roles
    public function index(): JsonResponse
    {
        $roles = Rol::with('permisos')->get();
        return response()->json($roles);
    }

    //  Mostrar un rol por ID
    public function show($id): JsonResponse
    {
        $rol = Rol::with('permisos')->find($id);

        if (is_null($rol)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($rol);
    }

    //  Crear nuevo rol
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'clave_rol' => 'required|string|max:100|unique:roles,clave_rol'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rol = Rol::create($validator->validated());
        return response()->json($rol, Response::HTTP_CREATED);
    }

    //  Actualizar rol existente
    public function update(Request $request, $id): JsonResponse
    {
        $rol = Rol::find($id);

        if (is_null($rol)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'clave_rol' => 'sometimes|required|string|max:100|unique:roles,clave_rol,' . $rol->id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rol->update($validator->validated());
        return response()->json($rol);
    }

    //  Eliminar rol
    public function destroy($id): JsonResponse
    {
        $rol = Rol::find($id);

        if (is_null($rol)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $rol->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

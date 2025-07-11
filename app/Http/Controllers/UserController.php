<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turista;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with(['turista', 'roles'])->get();
        return response()->json($users);
    }

    public function show($id): JsonResponse
    {
        $user = User::with(['turista', 'roles'])
            ->find($id);

        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function getRoles(): JsonResponse
    {
        $roles = Rol::all();
        return response()->json($roles);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'turista' => 'required|array',
            'turista.nombre' => 'required|string',
            'turista.apellido' => 'required|string',
            'turista.nacionalidad' => 'required|string',
            'turista.edad' => 'required|integer',
            'turista.telefono' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $validatedData = $validator->validated();

            $user = User::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            // Crear el turista asociado
            $turista = new Turista($validatedData['turista']);
            $turista->id_usuario = $user->id;
            $turista->save();

            // Asignar roles al usuario
            $user->roles()->attach($validatedData['roles']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user' => User::with(['turista', 'roles'])->find($user->id)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|min:6',
            'roles' => 'sometimes|required|array',
            'roles.*' => 'exists:roles,id',
            'turista' => 'sometimes|required|array',
            'turista.nombre' => 'sometimes|required|string',
            'turista.apellido' => 'sometimes|required|string',
            'turista.nacionalidad' => 'sometimes|required|string',
            'turista.edad' => 'sometimes|required|integer',
            'turista.telefono' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $validatedData = $validator->validated();

            if (isset($validatedData['email'])) {
                $user->email = $validatedData['email'];
            }
            if (isset($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }
            $user->save();

            if (isset($validatedData['turista'])) {
                $user->turista()->update($validatedData['turista']);
            }

            if (isset($validatedData['roles'])) {
                $user->roles()->sync($validatedData['roles']);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => User::with(['turista', 'roles'])->find($id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        DB::beginTransaction();
        try {
            $user->turista()->delete();
            $user->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

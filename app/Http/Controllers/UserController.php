<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turista;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['turista', 'roles'])->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with(['turista', 'roles'])
            ->findOrFail($id);
        return response()->json($user);
    }

    public function getRoles()
    {
        $roles = Rol::all();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
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

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Crear el turista asociado
            $turista = new Turista($request->turista);
            $turista->id_usuario = $user->id;
            $turista->save();

            // Asignar roles al usuario
            foreach ($request->roles as $roleId) {
                DB::table('usuarioxrol')->insert([
                    'id_usuario' => $user->id,
                    'id_rol' => $roleId
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'user' => User::with(['turista', 'roles'])->find($user->id)
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'email|unique:users,email,' . $id,
            'turista' => 'array',
            'turista.nombre' => 'string',
            'turista.apellido' => 'string',
            'turista.nacionalidad' => 'string',
            'turista.edad' => 'integer',
            'turista.telefono' => 'string'
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            if ($request->has('turista')) {
                $user->turista()->update($request->turista);
            }

            DB::commit();
            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'user' => User::with('turista')->find($id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->turista()->delete();
            $user->delete();

            DB::commit();
            return response()->json([
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

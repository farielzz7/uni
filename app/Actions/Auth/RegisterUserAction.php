<?php

namespace App\Actions\Auth;

use App\Models\Rol;
use App\Models\Turista;
use App\Models\User;
use App\Models\UsuarioXRol;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RegisterUserAction
{
    public function execute(array $data): array
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $turista = Turista::create([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'nacionalidad' => $data['nacionalidad'],
                'edad' => $data['edad'],
                'telefono' => $data['telefono'],
                'id_usuario' => $user->id,
            ]);

            $defaultRoleKey = Config::get('roles.default', 'turista');
            $defaultRole = Rol::where('clave_rol', $defaultRoleKey)->first();

            if (!$defaultRole) {
                throw new RuntimeException("No se encontró el rol por defecto '{$defaultRoleKey}'.");
            }

            UsuarioXRol::create([
                'id_usuario' => $user->id,
                'id_rol' => $defaultRole->id,
            ]);

            DB::commit();

            return ['user' => $user, 'turista' => $turista];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
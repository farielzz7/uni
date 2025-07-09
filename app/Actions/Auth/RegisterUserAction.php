<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\Turista;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

            // Asignar rol por defecto (turista)
            DB::table('usuarioxrol')->insert([
                'id_usuario' => $user->id,
                'id_rol' => 1 // ID del rol "turista"
            ]);

            DB::commit();

            return ['user' => $user, 'turista' => $turista];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
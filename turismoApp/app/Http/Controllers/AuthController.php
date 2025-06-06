<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turista;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request)
    {
        try {
            // Validación de datos
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'nombre' => 'required|string|min:2|max:50',
                'apellido' => 'required|string|min:2|max:50',
                'nacionalidad' => 'required|string|max:50',
                'edad' => 'required|integer|min:18|max:100',
                'telefono' => 'nullable|string|max:20',
                'acepta_terminos' => 'required|accepted'
            ], [
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe tener un formato válido',
                'email.unique' => 'Este email ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.confirmed' => 'Las contraseñas no coinciden',
                'nombre.required' => 'El nombre es obligatorio',
                'apellido.required' => 'El apellido es obligatorio',
                'nacionalidad.required' => 'La nacionalidad es obligatoria',
                'edad.required' => 'La edad es obligatoria',
                'edad.min' => 'Debes ser mayor de 18 años',
                'acepta_terminos.accepted' => 'Debes aceptar los términos y condiciones'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Crear usuario
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => now()
            ]);

            // Crear perfil de turista
            $turista = Turista::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'nacionalidad' => $request->nacionalidad,
                'edad' => $request->edad,
                'telefono' => $request->telefono,
                'id_usuario' => $user->id
            ]);

            // Asignar rol por defecto (turista)
            $this->asignarRolPorDefecto($user->id);

            // Generar token
            $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

            // Registrar sesión
            $this->registrarSesion($user->id, $token, $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso! Bienvenido a GoPlan',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'perfil' => [
                            'nombre' => $turista->nombre,
                            'apellido' => $turista->apellido,
                            'nacionalidad' => $turista->nacionalidad,
                            'edad' => $turista->edad,
                            'telefono' => $turista->telefono
                        ]
                    ],
                    'token' => $token,
                    'expires_at' => now()->addDays(30)->toISOString()
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en registro: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Inicio de sesión
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'remember_me' => 'nullable|boolean'
            ], [
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El email debe tener un formato válido',
                'password.required' => 'La contraseña es obligatoria'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar credenciales
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            $user = Auth::user();
            
            // Verificar si el usuario está activo
            if (!$this->usuarioEstaActivo($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuenta desactivada. Contacta al administrador'
                ], 403);
            }

            // Revocar tokens anteriores si no es "recordarme"
            if (!$request->remember_me) {
                $user->tokens()->delete();
            }

            // Generar nuevo token
            $expiresAt = $request->remember_me ? now()->addDays(30) : now()->addHours(24);
            $token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

            // Obtener información del turista
            $turista = $user->turista;

            // Registrar sesión
            $this->registrarSesion($user->id, $token, $request);

            // Actualizar último acceso
            $user->update(['last_login' => now()]);

            return response()->json([
                'success' => true,
                'message' => '¡Inicio de sesión exitoso!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'perfil' => $turista ? [
                            'nombre' => $turista->nombre,
                            'apellido' => $turista->apellido,
                            'nacionalidad' => $turista->nacionalidad,
                            'edad' => $turista->edad,
                            'telefono' => $turista->telefono
                        ] : null,
                        'roles' => $this->obtenerRolesUsuario($user->id)
                    ],
                    'token' => $token,
                    'expires_at' => $expiresAt->toISOString()
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Cierre de sesión
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            // Obtener token actual
            $currentToken = $request->bearerToken();
            
            // Marcar sesión como finalizada
            if ($currentToken) {
                $this->finalizarSesion($user->id, $currentToken);
            }

            // Revocar token actual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (Exception $e) {
            Log::error('Error en logout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cerrando sesión'
            ], 500);
        }
    }

    /**
     * Cerrar todas las sesiones
     */
    public function logoutAll(Request $request)
    {
        try {
            $user = $request->user();
            
            // Finalizar todas las sesiones activas
            $this->finalizarTodasLasSesiones($user->id);
            
            // Revocar todos los tokens
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Todas las sesiones han sido cerradas'
            ]);

        } catch (Exception $e) {
            Log::error('Error en logout all: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cerrando todas las sesiones'
            ], 500);
        }
    }

    /**
     * Información del usuario autenticado
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $turista = $user->turista;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'created_at' => $user->created_at,
                        'last_login' => $user->last_login,
                        'perfil' => $turista ? [
                            'nombre' => $turista->nombre,
                            'apellido' => $turista->apellido,
                            'nacionalidad' => $turista->nacionalidad,
                            'edad' => $turista->edad,
                            'telefono' => $turista->telefono
                        ] : null,
                        'roles' => $this->obtenerRolesUsuario($user->id),
                        'sesiones_activas' => $this->contarSesionesActivas($user->id)
                    ]
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error obteniendo usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo información del usuario'
            ], 500);
        }
    }

    /**
     * Refrescar token
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            
            // Revocar token actual
            $request->user()->currentAccessToken()->delete();
            
            // Crear nuevo token
            $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;
            
            // Actualizar sesión
            $this->actualizarSesion($user->id, $token, $request);

            return response()->json([
                'success' => true,
                'message' => 'Token renovado exitosamente',
                'data' => [
                    'token' => $token,
                    'expires_at' => now()->addHours(24)->toISOString()
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error refrescando token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error renovando token'
            ], 500);
        }
    }

    /**
     * Redireccionar a Facebook para autenticación
     */
    public function redirectToFacebook()
    {
        try {
            return response()->json([
                'success' => true,
                'redirect_url' => Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl()
            ]);
        } catch (Exception $e) {
            Log::error('Error redirigiendo a Facebook: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error conectando con Facebook'
            ], 500);
        }
    }

    /**
     * Manejar callback de Facebook
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            
            return $this->handleSocialAuth($facebookUser, 'facebook');

        } catch (Exception $e) {
            Log::error('Error en callback de Facebook: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error procesando autenticación de Facebook'
            ], 500);
        }
    }

    /**
     * Redireccionar a Google para autenticación
     */
    public function redirectToGoogle()
    {
        try {
            return response()->json([
                'success' => true,
                'redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
            ]);
        } catch (Exception $e) {
            Log::error('Error redirigiendo a Google: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error conectando con Google'
            ], 500);
        }
    }

    /**
     * Manejar callback de Google
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            return $this->handleSocialAuth($googleUser, 'google');

        } catch (Exception $e) {
            Log::error('Error en callback de Google: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error procesando autenticación de Google'
            ], 500);
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed|different:current_password'
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria',
                'new_password.required' => 'La nueva contraseña es obligatoria',
                'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
                'new_password.confirmed' => 'Las contraseñas no coinciden',
                'new_password.different' => 'La nueva contraseña debe ser diferente a la actual'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Verificar contraseña actual
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 401);
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Revocar todos los tokens excepto el actual
            $currentToken = $request->user()->currentAccessToken();
            $user->tokens()->where('id', '!=', $currentToken->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]);

        } catch (Exception $e) {
            Log::error('Error cambiando contraseña: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error actualizando contraseña'
            ], 500);
        }
    }

    // Métodos auxiliares privados

    private function handleSocialAuth($socialUser, $provider)
    {
        DB::beginTransaction();
        try {
            // Buscar usuario existente por email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Usuario existente - actualizar información social
                $user->update([
                    $provider . '_id' => $socialUser->getId(),
                    'last_login' => now()
                ]);
            } else {
                // Crear nuevo usuario
                $user = User::create([
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Password temporal
                    $provider . '_id' => $socialUser->getId(),
                    'created_at' => now()
                ]);

                // Crear perfil básico de turista
                $nombres = explode(' ', $socialUser->getName());
                $nombre = $nombres[0] ?? '';
                $apellido = isset($nombres[1]) ? implode(' ', array_slice($nombres, 1)) : '';

                Turista::create([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'nacionalidad' => 'No especificada',
                    'edad' => 18, // Valor por defecto
                    'id_usuario' => $user->id
                ]);

                // Asignar rol por defecto
                $this->asignarRolPorDefecto($user->id);
            }

            // Generar token
            $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

            // Registrar sesión
            $this->registrarSesion($user->id, $token, request());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Autenticación social exitosa',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'perfil' => $user->turista ? [
                            'nombre' => $user->turista->nombre,
                            'apellido' => $user->turista->apellido,
                            'nacionalidad' => $user->turista->nacionalidad,
                            'edad' => $user->turista->edad
                        ] : null
                    ],
                    'token' => $token,
                    'expires_at' => now()->addDays(30)->toISOString(),
                    'is_new_user' => !$user->wasRecentlyCreated
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function asignarRolPorDefecto($userId)
    {
        // Asignar rol de turista por defecto
        DB::table('usuarioxrol')->insert([
            'id_usuario' => $userId,
            'id_rol' => 1 // ID del rol "turista"
        ]);
    }

    private function registrarSesion($userId, $token, $request)
    {
        Sesion::create([
            'id_usuario' => $userId,
            'token' => substr($token, 0, 64), // Almacenar solo parte del token por seguridad
            'ip' => $request->ip(),
            'navegador' => $request->userAgent(),
            'fecha_inicio' => now(),
            'fecha_expiracion' => now()->addDays(30)
        ]);
    }

    private function finalizarSesion($userId, $token)
    {
        Sesion::where('id_usuario', $userId)
            ->where('token', substr($token, 0, 64))
            ->update(['fecha_expiracion' => now()]);
    }

    private function finalizarTodasLasSesiones($userId)
    {
        Sesion::where('id_usuario', $userId)
            ->where('fecha_expiracion', '>', now())
            ->update(['fecha_expiracion' => now()]);
    }

    private function actualizarSesion($userId, $token, $request)
    {
        // Finalizar sesión anterior y crear nueva
        $this->finalizarSesion($userId, $request->bearerToken());
        $this->registrarSesion($userId, $token, $request);
    }

    private function obtenerRolesUsuario($userId)
    {
        return DB::table('usuarioxrol')
            ->join('roles', 'usuarioxrol.id_rol', '=', 'roles.id')
            ->where('usuarioxrol.id_usuario', $userId)
            ->select('roles.nombre', 'roles.clave_rol')
            ->get();
    }

    private function usuarioEstaActivo($user)
    {
        // Verificar si el usuario está activo (puedes agregar más lógica)
        return true;
    }

    private function contarSesionesActivas($userId)
    {
        return Sesion::where('id_usuario', $userId)
            ->where('fecha_expiracion', '>', now())
            ->count();
    }
}
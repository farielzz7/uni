<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Actions\Auth\RegisterUserAction;
use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\LogoutUserAction;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct()
    {
        // Aplicar rate limiting al login para prevenir ataques de fuerza bruta
        $this->middleware('throttle:5,1')->only('login');
    }

    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request, RegisterUserAction $registerUserAction): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'nombre' => 'required|string|min:2|max:50',
                'apellido' => 'required|string|min:2|max:50',
                'nacionalidad' => 'required|string|max:50',
                'edad' => 'required|integer|min:18|max:100',
                'telefono' => 'nullable|string|max:20',
                'acepta_terminos' => 'required|accepted'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $result = $registerUserAction->execute($validator->validated());
            $user = $result['user'];
            $turista = $result['turista'];

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'perfil' => $turista
                    ],
                    'token' => $token,
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Inicio de sesión
     */
    public function login(Request $request, LoginUserAction $loginUserAction): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'remember_me' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember_me');

            if (!Auth::attempt($credentials, $remember)) {
                throw ValidationException::withMessages([
                    'email' => [__('auth.failed')],
                ]);
            }

            $user = User::where('email', $request->email)->with('turista', 'roles')->first();
            
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => '¡Inicio de sesión exitoso!',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
                'errors' => $e->errors()
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cierre de sesión
     */
    public function logout(Request $request, LogoutUserAction $logoutUserAction): JsonResponse
    {
        try {
            $logoutUserAction->execute($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en logout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cerrando sesión'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Información del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = User::with('turista', 'roles')->find(Auth::id());
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo información del usuario'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

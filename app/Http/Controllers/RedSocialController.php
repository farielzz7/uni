<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RedSocialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function index(): JsonResponse
    {
        $users = User::with(['turista', 'seguidores', 'siguiendo'])->get();
        return response()->json($users);
    }

    public function seguirUsuario(Request $request, $id): JsonResponse
    {
        $userToFollow = User::find($id);
        $currentUser = Auth::user();

        if (is_null($userToFollow)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario a seguir no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($currentUser->id === $userToFollow->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes seguirte a ti mismo'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($currentUser->isFollowing($userToFollow)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya estás siguiendo a este usuario'
            ], Response::HTTP_CONFLICT);
        }

        $currentUser->siguiendo()->attach($userToFollow->id);

        return response()->json([
            'success' => true,
            'message' => 'Usuario seguido correctamente'
        ], Response::HTTP_OK);
    }

    public function dejarDeSeguirUsuario(Request $request, $id): JsonResponse
    {
        $userToUnfollow = User::find($id);
        $currentUser = Auth::user();

        if (is_null($userToUnfollow)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario a dejar de seguir no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($currentUser->id === $userToUnfollow->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes dejar de seguirte a ti mismo'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$currentUser->isFollowing($userToUnfollow)) {
            return response()->json([
                'success' => false,
                'message' => 'No estás siguiendo a este usuario'
            ], Response::HTTP_CONFLICT);
        }

        $currentUser->siguiendo()->detach($userToUnfollow->id);

        return response()->json([
            'success' => true,
            'message' => 'Dejaste de seguir al usuario correctamente'
        ], Response::HTTP_OK);
    }
}

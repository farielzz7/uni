<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginUserAction
{
    /**
     * Attempt to authenticate a user and return the authenticated instance with a fresh token.
     */
    public function execute(array $credentials, bool $remember = false): ?array
    {
        if (!Auth::attempt($credentials, $remember)) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user()->load('turista', 'roles');

        // Invalidate previous tokens to avoid leaked sessions.
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

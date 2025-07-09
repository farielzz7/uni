<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginUserAction
{
    public function execute(array $credentials, bool $remember = false): ?User
    {
        if (Auth::attempt($credentials, $remember)) {
            return Auth::user();
        }

        return null;
    }
}
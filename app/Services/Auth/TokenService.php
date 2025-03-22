<?php

namespace App\Services\Auth;

use App\Models\User;

class TokenService
{
    public function generateToken(User $user): string
    {
        return $user->createToken('API Token')->plainTextToken;
    }
}

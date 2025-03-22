<?php

namespace App\Services\Validation;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

class PasswordService
{
    public function validatePassword(string $password, string $hashedPassword)
    {
        if (!Hash::check($password, $hashedPassword)) {
            throw new AuthenticationException('Invalid credentials');
        }
    }
}

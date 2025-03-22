<?php

namespace App\Repositories\Auth;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser(array $data): User;
    public function findByEmail(string $email): ?User;
    public function deleteTokens(User $user): void;
}

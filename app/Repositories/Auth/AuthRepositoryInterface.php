<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Repositories\BaseRepositoryInterface;

interface AuthRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findByTenantId(int $tenantId): ?User;
    public function deleteTokens(User $user): void;
}

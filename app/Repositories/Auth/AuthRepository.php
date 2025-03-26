<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Repositories\BaseRepository;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findByColumn('email', $email);
    }

    public function findByTenantId(int $tenantId): ?User
    {
        return $this->findByColumn('tenant_id', $tenantId);
    }

    public function deleteTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}

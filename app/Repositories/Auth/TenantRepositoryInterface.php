<?php

namespace App\Repositories\Auth;

use App\Models\Tenant;

interface TenantRepositoryInterface
{
    public function createTenant(array $data): Tenant;
    public function findBySubdomain(string $subdomain): ?Tenant;
}

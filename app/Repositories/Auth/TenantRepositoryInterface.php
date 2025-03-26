<?php

namespace App\Repositories\Auth;

use App\Models\Tenant;
use App\Repositories\BaseRepositoryInterface;

interface TenantRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySubdomain(string $subdomain): ?Tenant;
}

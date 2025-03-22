<?php

namespace App\Repositories\Auth;

use App\Models\Tenant;
use App\Repositories\Auth\TenantRepositoryInterface;

class TenantRepository implements TenantRepositoryInterface
{
    public function createTenant(array $data): Tenant
    {
        // Create a new tenant
        return Tenant::create([
            'subdomain' => $data['subdomain'],
        ]);
    }

    public function findBySubdomain($subdomain): Tenant
    {
        // Find a tenant by subdomain
        return Tenant::where('subdomain', $subdomain)->first();
    }
}

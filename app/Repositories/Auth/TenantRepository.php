<?php

namespace App\Repositories\Auth;

use App\Models\Tenant;
use App\Repositories\BaseRepository;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $tenant)
    {
        parent::__construct($tenant);
    }

    public function findBySubdomain($subdomain): ?Tenant
    {
        return $this->findByColumn('subdomain', $subdomain);
    }
}

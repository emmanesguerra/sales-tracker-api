<?php 

namespace App\Services\Auth;

use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantService
{
    public function generateSubdomain(string $tenantName): string
    {
        // Step 1: Slugify the tenant's name
        $subdomain = Str::slug($tenantName);

        // Step 2: Ensure the subdomain is unique
        while (Tenant::where('subdomain', $subdomain)->exists()) {
            // If the subdomain already exists, append a number to make it unique
            $subdomain = $subdomain . rand(100, 999);
        }

        return $subdomain;
    }
}

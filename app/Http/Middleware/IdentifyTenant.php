<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0]; // Extract the subdomain

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Store tenant in request
        $request->merge(['tenant' => $tenant]);

        // If user is authenticated, ensure they belong to this tenant
        if (Auth::check() && Auth::user()->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to this tenant');
        }

        return $next($request);
    }
}

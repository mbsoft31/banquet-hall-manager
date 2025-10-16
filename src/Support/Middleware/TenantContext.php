<?php

namespace Mbsoft\BanquetHallManager\Support\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantContext
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('banquethallmanager.multi_tenancy')) {
            return $next($request);
        }

        $userTenant = optional($request->user())->tenant_id;
        $headerTenant = $request->header('X-Tenant-ID');

        // If user has a tenant, enforce it and override header
        if ($userTenant) {
            $request->headers->set('X-Tenant-ID', (string) $userTenant, true);
            return $next($request);
        }

        // Else require header if enforce_tenant_header is true
        if (config('banquethallmanager.enforce_tenant_header', true)) {
            if (!$headerTenant) {
                return response()->json([
                    'message' => 'Tenant context required (X-Tenant-ID).'
                ], 400);
            }
        }

        return $next($request);
    }
}


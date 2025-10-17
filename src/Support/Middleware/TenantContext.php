<?php

namespace Mbsoft\BanquetHallManager\Support\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantContext
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('banquethallmanager.multi_tenancy', false)) {
            return $next($request);
        }

        $tenantId = optional($request->user())->tenant_id;

        if ($tenantId) {
            $request->headers->set('X-Tenant-ID', (string) $tenantId, true);
        } else {
            $tenantId = $request->header('X-Tenant-ID');
        }

        if (!$tenantId && config('banquethallmanager.enforce_tenant_header', false)) {
            return response()->json([
                'message' => 'Tenant context required (X-Tenant-ID).'
            ], 400);
        }

        if ($tenantId) {
            config(['banquethallmanager.current_tenant_id' => (int) $tenantId]);
        }

        return $next($request);
    }
}


<?php

namespace Mbsoft\BanquetHallManager\Policies\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;

trait ResolvesTenant
{
    protected function currentTenantId(?Authenticatable $user): ?int
    {
        $tid = ($user?->tenant_id) ?? request()->header('X-Tenant-ID');
        return $tid !== null ? (int) $tid : null;
    }
}

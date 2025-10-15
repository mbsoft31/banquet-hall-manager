<?php

namespace Mbsoft\BanquetHallManager\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Mbsoft\BanquetHallManager\Models\Staff;
use Mbsoft\BanquetHallManager\Policies\Concerns\ResolvesTenant;

class StaffPolicy
{
    use ResolvesTenant;

    public function viewAny(?Authenticatable $user): bool
    {
        if (!Gate::forUser($user)->allows('bhm.read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        return (bool) $this->currentTenantId($user);
    }

    public function view(?Authenticatable $user, Staff $staff): bool
    {
        if (!Gate::forUser($user)->allows('bhm.read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? (int) $staff->tenant_id === $tenantId : false;
    }

    public function create(?Authenticatable $user): bool
    {
        if (!Gate::forUser($user)->allows('bhm.write')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        return (bool) $this->currentTenantId($user);
    }

    public function update(?Authenticatable $user, Staff $staff): bool
    {
        return Gate::forUser($user)->allows('bhm.write');
    }

    public function delete(?Authenticatable $user, Staff $staff): bool
    {
        return Gate::forUser($user)->allows('bhm.delete');
    }
}


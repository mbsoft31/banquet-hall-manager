<?php

namespace Mbsoft\BanquetHallManager\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Policies\Concerns\ResolvesTenant;

class HallPolicy
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

    public function view(?Authenticatable $user, Hall $hall): bool
    {
        if (!Gate::forUser($user)->allows('bhm.read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? (int) $hall->tenant_id === $tenantId : false;
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

    public function update(?Authenticatable $user, Hall $hall): bool
    {
        if (!Gate::forUser($user)->allows('bhm.write')) {
            return false;
        }
        return $this->view($user, $hall);
    }

    public function delete(?Authenticatable $user, Hall $hall): bool
    {
        if (!Gate::forUser($user)->allows('bhm.delete')) {
            return false;
        }
        return $this->view($user, $hall);
    }
}

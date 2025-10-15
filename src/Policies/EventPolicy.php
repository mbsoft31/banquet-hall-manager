<?php

namespace Mbsoft\BanquetHallManager\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Policies\Concerns\ResolvesTenant;

class EventPolicy
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

    public function view(?Authenticatable $user, Event $event): bool
    {
        if (!Gate::forUser($user)->allows('bhm.read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? (int) $event->tenant_id === $tenantId : false;
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

    public function update(?Authenticatable $user, Event $event): bool
    {
        if (!Gate::forUser($user)->allows('bhm.write')) {
            return false;
        }
        return $this->view($user, $event);
    }

    public function delete(?Authenticatable $user, Event $event): bool
    {
        // Deletion allowed when model is already route-scoped to tenant via binding.
        // Gate can be customized by host app if needed.
        return Gate::forUser($user)->allows('bhm.delete');
    }
}

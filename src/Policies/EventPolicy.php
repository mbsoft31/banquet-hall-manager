<?php

namespace Mbsoft\BanquetHallManager\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Policies\Concerns\ResolvesTenant;

class EventPolicy
{
    use ResolvesTenant;

    public function viewAny(?Authenticatable $user): bool
    {
        return true;
    }

    public function view(?Authenticatable $user, Event $event): bool
    {
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? (int) $event->tenant_id === $tenantId : false;
    }

    public function create(?Authenticatable $user): bool
    {
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        return (bool) $this->currentTenantId($user);
    }

    public function update(?Authenticatable $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function delete(?Authenticatable $user, Event $event): bool
    {
        // For scaffold, allow deletion when route binding has already scoped the model
        // to tenant; tighten this as auth requirements evolve.
        return true;
    }
}

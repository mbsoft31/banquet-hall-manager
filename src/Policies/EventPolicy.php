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
        if (!$this->canPerform($user, 'read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        return (bool) $this->currentTenantId($user);
    }

    public function view(?Authenticatable $user, Event $event): bool
    {
        if (!$this->canPerform($user, 'read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? $this->tenantMatches($event, $tenantId) : false;
    }

    public function create(?Authenticatable $user): bool
    {
        if (!$this->canPerform($user, 'write')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        return (bool) $this->currentTenantId($user);
    }

    public function update(?Authenticatable $user, Event $event): bool
    {
        if (!$this->canPerform($user, 'write')) {
            return false;
        }

        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }

        $tenantId = $this->currentTenantId($user);

        return $tenantId ? $this->tenantMatches($event, $tenantId) : false;
    }

    public function delete(?Authenticatable $user, Event $event): bool
    {
        if (!$this->canPerform($user, 'delete')) {
            return false;
        }

        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }

        $tenantId = $this->currentTenantId($user);

        return $tenantId ? $this->tenantMatches($event, $tenantId) : false;
    }

    protected function canPerform(?Authenticatable $user, string $ability): bool
    {
        if (!$user) {
            return false;
        }

        $permission = config("banquethallmanager.permissions.{$ability}");
        if (is_string($permission) && Gate::has($permission)) {
            return Gate::forUser($user)->allows($permission);
        }

        $role = $user->role ?? null;
        $roles = config("banquethallmanager.roles.{$ability}", []);

        if (empty($roles)) {
            return true;
        }

        return $role !== null && in_array($role, $roles, true);
    }

    protected function tenantMatches(Event $event, int $tenantId): bool
    {
        if ($event->tenant_id === null) {
            return true;
        }

        return (int) $event->tenant_id === $tenantId;
    }
}

<?php

namespace Mbsoft\BanquetHallManager\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Mbsoft\BanquetHallManager\Models\Payment;
use Mbsoft\BanquetHallManager\Policies\Concerns\ResolvesTenant;

class PaymentPolicy
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

    public function view(?Authenticatable $user, Payment $payment): bool
    {
        if (!$this->canPerform($user, 'read')) {
            return false;
        }
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? (int) $payment->tenant_id === $tenantId : false;
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
}


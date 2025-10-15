<?php

namespace Mbsoft\BanquetHallManager\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Policies\Concerns\ResolvesTenant;

class ClientPolicy
{
    use ResolvesTenant;

    public function viewAny(?Authenticatable $user): bool
    {
        return true;
    }

    public function view(?Authenticatable $user, Client $client): bool
    {
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        $tenantId = $this->currentTenantId($user);
        return $tenantId ? (int) $client->tenant_id === $tenantId : false;
    }

    public function create(?Authenticatable $user): bool
    {
        if (!config('banquethallmanager.multi_tenancy')) {
            return true;
        }
        return (bool) $this->currentTenantId($user);
    }

    public function update(?Authenticatable $user, Client $client): bool
    {
        return $this->view($user, $client);
    }

    public function delete(?Authenticatable $user, Client $client): bool
    {
        return $this->view($user, $client);
    }
}


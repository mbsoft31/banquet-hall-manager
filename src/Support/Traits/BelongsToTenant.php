<?php

namespace Mbsoft\BanquetHallManager\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (config('banquethallmanager.multi_tenancy') && empty($model->tenant_id)) {
                $tenantId = static::resolveTenantId();
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $q): void {
            if (!config('banquethallmanager.multi_tenancy')) {
                return;
            }
            $tenantId = static::resolveTenantId();
            if ($tenantId) {
                $q->where($q->getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $tenantId = static::resolveTenantId();
        if (config('banquethallmanager.multi_tenancy') && $tenantId) {
            $query->where($this->getTable() . '.tenant_id', $tenantId);
        }

        return $query->where($field ?? $this->getRouteKeyName(), $value);
    }

    protected static function resolveTenantId(): ?int
    {
        $userTenant = optional(auth()->user())->tenant_id;
        if ($userTenant) {
            return (int) $userTenant;
        }

        $request = app()->bound('request') ? request() : null;
        $headerTenant = $request?->header('X-Tenant-ID');
        if ($headerTenant) {
            return (int) $headerTenant;
        }

        $configuredTenant = config('banquethallmanager.current_tenant_id')
            ?? config('banquethallmanager.default_tenant_id');

        return $configuredTenant !== null ? (int) $configuredTenant : null;
    }
}

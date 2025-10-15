<?php

namespace Mbsoft\BanquetHallManager\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (config('banquethallmanager.multi_tenancy') && empty($model->tenant_id)) {
                $tenantId = optional(auth()->user())->tenant_id ?? request()->header('X-Tenant-ID');
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $q): void {
            if (!config('banquethallmanager.multi_tenancy')) {
                return;
            }
            $tenantId = optional(auth()->user())->tenant_id ?? request()->header('X-Tenant-ID');
            if ($tenantId) {
                $q->where($q->getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });
    }
}


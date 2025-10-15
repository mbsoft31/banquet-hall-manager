<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class ServiceType extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_service_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'default_price',
        'unit',
        'is_active',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}


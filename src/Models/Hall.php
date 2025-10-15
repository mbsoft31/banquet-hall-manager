<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $table = 'bhm_halls';

    protected $fillable = [
        'tenant_id',
        'name',
        'capacity',
        'location',
        'description',
        'hourly_rate',
        'amenities',
        'status',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];
}


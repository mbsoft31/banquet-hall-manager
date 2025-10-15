<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class Event extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_events';

    protected $fillable = [
        'tenant_id',
        'hall_id',
        'client_id',
        'name',
        'type',
        'start_at',
        'end_at',
        'guest_count',
        'status',
        'special_requests',
        'total_amount',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'special_requests' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}


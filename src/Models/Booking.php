<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class Booking extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_bookings';

    protected $fillable = [
        'tenant_id',
        'event_id',
        'description',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}


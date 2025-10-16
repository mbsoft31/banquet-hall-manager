<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\BookingFactory;

class Booking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_bookings';

    protected $fillable = [
        'tenant_id',
        'event_id',
        'service_type_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function newFactory()
    {
        return BookingFactory::new();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
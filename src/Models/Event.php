<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\EventFactory;

class Event extends Model
{
    use HasFactory, BelongsToTenant;

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

    protected static function newFactory()
    {
        return EventFactory::new();
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'event_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'bhm_event_staff');
    }
}
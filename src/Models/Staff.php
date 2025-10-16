<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\StaffFactory;

class Staff extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_staff';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'position',
        'hourly_rate',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    protected static function newFactory()
    {
        return StaffFactory::new();
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'bhm_event_staff');
    }
}
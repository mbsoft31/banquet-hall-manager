<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\ServiceTypeFactory;

class ServiceType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_service_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'default_price',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
    ];

    protected static function newFactory()
    {
        return ServiceTypeFactory::new();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
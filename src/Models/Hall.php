<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\HallFactory;

class Hall extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_halls';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'capacity',
        'hourly_rate',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    protected static function newFactory()
    {
        return HallFactory::new();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
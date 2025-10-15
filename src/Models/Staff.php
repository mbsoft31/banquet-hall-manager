<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class Staff extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_staff';

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'bhm_event_staff');
    }
}


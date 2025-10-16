<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\ClientFactory;

class Client extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_clients';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'address',
    ];

    protected static function newFactory()
    {
        return ClientFactory::new();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class Client extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_clients';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'notes',
    ];
}


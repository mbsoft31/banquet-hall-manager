<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class Payment extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_payments';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'amount',
        'method',
        'reference',
        'cash_tendered',
        'change_given',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cash_tendered' => 'decimal:2',
        'change_given' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}


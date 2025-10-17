<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\PaymentFactory;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_payments';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'amount',
        'payment_method',
        'payment_date',
        'transaction_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_date' => 'date',
    ];

    protected static function newFactory()
    {
        return PaymentFactory::new();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

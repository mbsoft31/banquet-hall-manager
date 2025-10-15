<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $table = 'bhm_invoices';

    protected $fillable = [
        'tenant_id',
        'event_id',
        'client_id',
        'invoice_number',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'due_date',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}


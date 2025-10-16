<?php

namespace Mbsoft\BanquetHallManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mbsoft\BanquetHallManager\Support\Traits\BelongsToTenant;
use Mbsoft\BanquetHallManager\Database\Factories\InvoiceFactory;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bhm_invoices';

    protected $fillable = [
        'tenant_id',
        'event_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function newFactory()
    {
        return InvoiceFactory::new();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
<?php

namespace Mbsoft\BanquetHallManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'client_id' => $this->client_id,
            'invoice_number' => $this->invoice_number,
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'total_amount' => (float) $this->total_amount,
            'due_date' => optional($this->due_date)->toDateString(),
            'status' => $this->status,
        ];
    }
}


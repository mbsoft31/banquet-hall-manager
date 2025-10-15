<?php

namespace Mbsoft\BanquetHallManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'amount' => (float) $this->amount,
            'method' => $this->method,
            'reference' => $this->reference,
            'cash_tendered' => $this->cash_tendered !== null ? (float) $this->cash_tendered : null,
            'change_given' => (float) $this->change_given,
            'paid_at' => optional($this->paid_at)->toDateTimeString(),
            'status' => $this->status,
        ];
    }
}


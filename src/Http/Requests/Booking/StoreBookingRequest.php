<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}


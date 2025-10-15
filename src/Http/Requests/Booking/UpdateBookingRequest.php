<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'unit_price' => ['sometimes', 'numeric', 'min:0'],
            'total_price' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}


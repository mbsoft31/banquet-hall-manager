<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hall_id' => ['required', 'integer'],
            'client_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'guest_count' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['draft', 'confirmed', 'cancelled', 'completed'])],
            'special_requests' => ['nullable', 'array'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}


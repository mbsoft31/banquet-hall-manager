<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hall_id' => ['sometimes', 'integer'],
            'client_id' => ['sometimes', 'integer'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:100'],
            'start_at' => ['sometimes', 'date'],
            'end_at' => ['sometimes', 'date', 'after:start_at'],
            'guest_count' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'confirmed', 'cancelled', 'completed'])],
            'special_requests' => ['sometimes', 'array'],
            'total_amount' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}


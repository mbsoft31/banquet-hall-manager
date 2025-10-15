<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Hall;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'capacity' => ['sometimes', 'integer', 'min:0'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'hourly_rate' => ['sometimes', 'numeric', 'min:0'],
            'amenities' => ['sometimes', 'array'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}


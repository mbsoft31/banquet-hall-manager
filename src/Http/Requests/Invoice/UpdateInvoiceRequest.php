<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::in(['pending', 'paid', 'overdue', 'cancelled'])],
            'due_date' => ['sometimes', 'date'],
        ];
    }
}


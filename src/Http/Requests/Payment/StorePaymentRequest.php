<?php

namespace Mbsoft\BanquetHallManager\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $methods = (array) config('banquethallmanager.payment_methods', ['cash']);
        return [
            'invoice_id' => ['required', 'integer', 'exists:bhm_invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:'.implode(',', $methods)],
            'payment_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:pending,completed,failed,cancelled'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}


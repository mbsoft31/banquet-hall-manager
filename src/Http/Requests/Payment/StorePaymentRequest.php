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
            'invoice_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'in:'.implode(',', $methods)],
            'reference' => ['nullable', 'string', 'max:255'],
            'cash_tendered' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}


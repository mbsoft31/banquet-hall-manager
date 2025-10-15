<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Payment\StorePaymentRequest;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Payment;

class PaymentController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Payment::class);
        $query = Payment::query();
        if ($invoiceId = request()->query('invoice_id')) {
            $query->where('invoice_id', (int) $invoiceId);
        }
        $per = (int) (request()->query('per_page', 15));
        return response()->json($query->orderByDesc('id')->paginate($per));
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return response()->json($payment);
    }

    public function store(StorePaymentRequest $request)
    {
        $this->authorize('create', Payment::class);
        $data = $request->validated();

        $invoice = Invoice::findOrFail((int) $data['invoice_id']);

        $amount = (float) $data['amount'];
        $method = (string) $data['method'];
        $tendered = isset($data['cash_tendered']) ? (float) $data['cash_tendered'] : null;

        if ($method === 'cash') {
            if ($tendered !== null && $tendered < $amount) {
                return response()->json(['message' => 'Insufficient cash tendered.'], 422);
            }
        }

        $data['paid_at'] = Carbon::now();
        $data['change_given'] = 0;
        if ($method === 'cash' && $tendered !== null) {
            $data['change_given'] = round($tendered - $amount, 2);
        }

        unset($data['tenant_id']);

        $payment = Payment::create($data);

        // Update invoice status based on total payments
        $sum = (float) Payment::where('invoice_id', $invoice->id)->sum('amount');
        if ($sum >= (float) $invoice->total_amount && $invoice->status !== 'paid') {
            $invoice->status = 'paid';
            $invoice->save();
        }

        return response()->json($payment, 201);
    }
}


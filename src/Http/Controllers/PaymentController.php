<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Payment\StorePaymentRequest;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Payment;
use Mbsoft\BanquetHallManager\Http\Resources\PaymentResource;

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
        $allowedSorts = ['id','invoice_id','amount','payment_method','payment_date','status','created_at'];
        $sort = request()->query('sort');
        $dir = strtolower((string) request()->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderByDesc('id');
        }
        $per = (int) (request()->query('per_page', 15));
        return PaymentResource::collection($query->paginate($per));
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return PaymentResource::make($payment);
    }

    public function store(StorePaymentRequest $request)
    {
        $this->authorize('create', Payment::class);
        $data = $request->validated();

        $invoice = Invoice::findOrFail((int) $data['invoice_id']);

        $requestedAmount = (float) $data['amount'];
        $alreadyPaid = (float) Payment::where('invoice_id', $invoice->id)->sum('amount');
        $invoiceTotal = (float) $invoice->total_amount;
        $balance = max(0.0, round($invoiceTotal - $alreadyPaid, 2));

        if ($requestedAmount > $balance) {
            return response()->json([
                'message' => 'Payment amount exceeds invoice balance.',
                'errors' => [
                    'amount' => ['Payment amount exceeds invoice balance.'],
                ],
            ], 422);
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $requestedAmount,
            'payment_method' => $data['payment_method'],
            'payment_date' => $data['payment_date'] ?? Carbon::now()->toDateString(),
            'status' => $data['status'] ?? 'completed',
            'transaction_id' => $data['transaction_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $paidTotal = $alreadyPaid + $requestedAmount;
        if ($paidTotal >= $invoiceTotal) {
            $invoice->status = 'paid';
        } elseif ($paidTotal > 0) {
            $invoice->status = 'partial';
        }

        $invoice->save();

        return PaymentResource::make($payment)->response()->setStatusCode(201);
    }
}

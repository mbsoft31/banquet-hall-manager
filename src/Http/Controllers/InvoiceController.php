<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Invoice\UpdateInvoiceRequest;
use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Http\Resources\InvoiceResource;

class InvoiceController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Invoice::class);
        $query = Invoice::query();
        if ($status = request()->query('status')) {
            $query->where('status', $status);
        }
        if ($clientId = request()->query('client_id')) {
            $query->where('client_id', (int) $clientId);
        }
        if ($eventId = request()->query('event_id')) {
            $query->where('event_id', (int) $eventId);
        }
        $allowedSorts = ['id','invoice_number','status','due_date','total_amount','created_at'];
        $sort = request()->query('sort');
        $dir = strtolower((string) request()->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $dir);
        }

        $perPage = (int) (request()->query('per_page', 15));
        return InvoiceResource::collection($query->paginate($perPage));
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        return InvoiceResource::make($invoice);
    }

    public function storeFromEvent(int $event)
    {
        $this->authorize('create', Invoice::class);

        $eventModel = Event::findOrFail($event);
        $client = Client::findOrFail($eventModel->client_id);
        $bookings = Booking::where('event_id', $eventModel->id)->get();

        $subtotal = (float) $bookings->sum('total_price');
        $tax = (float) ($subtotal * (float) config('banquethallmanager.tax_rate', 0));
        $discount = 0.0;
        $total = $subtotal + $tax - $discount;

        $invoice = Invoice::create([
            'event_id' => $eventModel->id,
            'client_id' => $client->id,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'due_date' => Carbon::now()->addDays((int) config('banquethallmanager.payment_due_days', 30)),
            'status' => 'pending',
        ]);

        $prefix = (string) config('banquethallmanager.invoice_prefix', 'BHM');
        $invoice->invoice_number = sprintf('%s-%s-%d', $prefix, now()->format('Ymd'), $invoice->id);
        $invoice->save();

        return InvoiceResource::make($invoice)->response()->setStatusCode(201);
    }

    public function update(UpdateInvoiceRequest $request, int $invoice)
    {
        $model = Invoice::findOrFail($invoice);
        $this->authorize('update', $model);
        $model->update($request->validated());
        return InvoiceResource::make(Invoice::findOrFail($invoice));
    }

    public function balance(int $invoice)
    {
        $model = Invoice::findOrFail($invoice);
        $this->authorize('view', $model);
        $total = (float) $model->total_amount;
        $paid = (float) \Mbsoft\BanquetHallManager\Models\Payment::where('invoice_id', $model->id)->sum('amount');
        $due = max(0.0, round($total - $paid, 2));
        return response()->json([
            'invoice_id' => $model->id,
            'total' => $total,
            'paid' => $paid,
            'due' => $due,
        ]);
    }
}

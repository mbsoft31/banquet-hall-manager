<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Booking\StoreBookingRequest;
use Mbsoft\BanquetHallManager\Http\Requests\Booking\UpdateBookingRequest;
use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\Event;

class BookingController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Booking::class);
        $query = Booking::query();
        if ($eventId = request()->query('event_id')) {
            $query->where('event_id', (int) $eventId);
        }
        $perPage = (int) (request()->query('per_page', 15));
        return response()->json($query->paginate($perPage));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return response()->json($booking);
    }

    public function store(StoreBookingRequest $request)
    {
        $this->authorize('create', Booking::class);
        $data = $request->validated();

        $event = Event::findOrFail($data['event_id']);

        $qty = (int) ($data['quantity'] ?? 1);
        $unit = (float) ($data['unit_price'] ?? 0);
        $data['quantity'] = $qty;
        $data['unit_price'] = $unit;
        $data['total_price'] = $data['total_price'] ?? ($qty * $unit);
        unset($data['tenant_id']);

        $booking = Booking::create($data);
        return response()->json($booking, 201);
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        $data = $request->validated();

        if (array_key_exists('quantity', $data) || array_key_exists('unit_price', $data)) {
            $qty = (int) ($data['quantity'] ?? $booking->quantity);
            $unit = (float) ($data['unit_price'] ?? $booking->unit_price);
            $data['total_price'] = $qty * $unit;
        }
        unset($data['tenant_id']);

        $booking->update($data);
        return response()->json($booking);
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        $booking->delete();
        return response()->noContent();
    }
}


<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;

class EventController extends BaseController
{
    public function index(Request $request)
    {
        $query = Event::query()->with(['hall:id,name', 'client:id,name']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($hallId = $request->query('hall_id')) {
            $query->where('hall_id', $hallId);
        }
        if ($clientId = $request->query('client_id')) {
            $query->where('client_id', $clientId);
        }
        if ($from = $request->query('from')) {
            $query->where('start_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->where('end_at', '<=', $to);
        }

        $perPage = (int) ($request->query('per_page', 15));
        return response()->json($query->paginate($perPage));
    }

    public function show(Event $event)
    {
        $event->load(['hall:id,name', 'client:id,name']);
        $payload = array_merge($event->attributesToArray(), [
            'hall' => $event->hall ? $event->hall->only(['id', 'name']) : null,
            'client' => $event->client ? $event->client->only(['id', 'name']) : null,
        ]);
        return response()->json($payload);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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
        ]);

        // Ensure referenced hall and client belong to tenant (via global scope on models)
        $hall = Hall::findOrFail($data['hall_id']);
        $client = Client::findOrFail($data['client_id']);

        unset($data['tenant_id']);

        $event = Event::create($data);
        $event->load(['hall:id,name', 'client:id,name']);
        $payload = array_merge($event->attributesToArray(), [
            'hall' => $event->hall ? $event->hall->only(['id', 'name']) : null,
            'client' => $event->client ? $event->client->only(['id', 'name']) : null,
        ]);
        return response()->json($payload, 201);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
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
        ]);

        if (array_key_exists('hall_id', $data)) {
            Hall::findOrFail($data['hall_id']);
        }
        if (array_key_exists('client_id', $data)) {
            Client::findOrFail($data['client_id']);
        }

        unset($data['tenant_id']);

        $event->update($data);
        $event->load(['hall:id,name', 'client:id,name']);
        $payload = array_merge($event->attributesToArray(), [
            'hall' => $event->hall ? $event->hall->only(['id', 'name']) : null,
            'client' => $event->client ? $event->client->only(['id', 'name']) : null,
        ]);
        return response()->json($payload);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->noContent();
    }
}

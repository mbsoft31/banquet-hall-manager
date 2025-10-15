<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Event\StoreEventRequest;
use Mbsoft\BanquetHallManager\Http\Requests\Event\UpdateEventRequest;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;

class EventController extends BaseController
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Event::class);
        $query = Event::query()->with(['hall:id,name', 'client:id,name']);

        if ($status = request()->query('status')) {
            $query->where('status', $status);
        }
        if ($hallId = request()->query('hall_id')) {
            $query->where('hall_id', $hallId);
        }
        if ($clientId = request()->query('client_id')) {
            $query->where('client_id', $clientId);
        }
        if ($from = request()->query('from')) {
            $query->where('start_at', '>=', $from);
        }
        if ($to = request()->query('to')) {
            $query->where('end_at', '<=', $to);
        }

        $perPage = (int) (request()->query('per_page', 15));
        return response()->json($query->paginate($perPage));
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        $event->load(['hall:id,name', 'client:id,name']);
        $payload = array_merge($event->attributesToArray(), [
            'hall' => $event->hall ? $event->hall->only(['id', 'name']) : null,
            'client' => $event->client ? $event->client->only(['id', 'name']) : null,
        ]);
        return response()->json($payload);
    }

    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);
        $data = $request->validated();

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

    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $data = $request->validated();

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
        $this->authorize('delete', $event);
        $event->delete();
        return response()->noContent();
    }
}

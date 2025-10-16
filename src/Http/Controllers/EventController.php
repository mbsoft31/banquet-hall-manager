<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Event\StoreEventRequest;
use Mbsoft\BanquetHallManager\Http\Requests\Event\UpdateEventRequest;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Http\Resources\EventResource;

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

        $allowedSorts = ['id','name','type','start_at','end_at','status','created_at'];
        $sort = request()->query('sort');
        $dir = strtolower((string) request()->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $dir);
        }

        $perPage = (int) (request()->query('per_page', 15));
        return EventResource::collection($query->paginate($perPage));
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        $event->load(['hall:id,name', 'client:id,name']);
        return EventResource::make($event);
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
        return EventResource::make($event)->response()->setStatusCode(201);
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
        return EventResource::make($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();
        return response()->noContent();
    }

    protected function hasConflict(int $hallId, string $startAt, string $endAt, ?int $excludeId = null): bool
    {
        $q = Event::query()->where('hall_id', $hallId)
            ->where(function ($w) use ($startAt, $endAt) {
                $w->whereBetween('start_at', [$startAt, $endAt])
                  ->orWhereBetween('end_at', [$startAt, $endAt])
                  ->orWhere(function ($x) use ($startAt, $endAt) {
                      $x->where('start_at', '<=', $startAt)->where('end_at', '>=', $endAt);
                  });
            });
        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }
        return $q->exists();
    }

    public function reschedule(int $event)
    {
        $eventModel = Event::findOrFail($event);
        $this->authorize('update', $eventModel);
        $data = request()->validate([
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ]);
        if ($this->hasConflict($eventModel->hall_id, $data['start_at'], $data['end_at'], $eventModel->id)) {
            return response()->json(['message' => 'Scheduling conflict for hall.'], 422);
        }
        $eventModel->update($data);
        return EventResource::make($eventModel->refresh());
    }

    public function cancel(int $event)
    {
        $eventModel = Event::findOrFail($event);
        $this->authorize('update', $eventModel);
        $eventModel->update(['status' => 'cancelled']);
        return EventResource::make($eventModel->refresh());
    }
}

<?php

namespace Mbsoft\BanquetHallManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'hall_id' => $this->hall_id,
            'client_id' => $this->client_id,
            'name' => $this->name,
            'type' => $this->type,
            'start_at' => optional($this->start_at)->toDateTimeString(),
            'end_at' => optional($this->end_at)->toDateTimeString(),
            'guest_count' => (int) $this->guest_count,
            'status' => $this->status,
            'special_requests' => $this->special_requests,
            'total_amount' => $this->total_amount !== null ? (float) $this->total_amount : null,
            'hall' => $this->whenLoaded('hall', fn () => [
                'id' => $this->hall->id,
                'name' => $this->hall->name,
            ]),
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ]),
        ];
    }
}


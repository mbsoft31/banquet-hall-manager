<?php

namespace Mbsoft\BanquetHallManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'location' => $this->location,
            'description' => $this->description,
            'hourly_rate' => (float) $this->hourly_rate,
            'amenities' => $this->amenities,
            'status' => $this->status,
        ];
    }
}


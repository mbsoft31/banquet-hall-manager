<?php

namespace Mbsoft\BanquetHallManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'role' => $this->role,
            'is_active' => (bool) $this->is_active,
        ];
    }
}


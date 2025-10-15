<?php

namespace Mbsoft\BanquetHallManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'default_price' => (float) $this->default_price,
            'unit' => $this->unit,
            'is_active' => (bool) $this->is_active,
        ];
    }
}


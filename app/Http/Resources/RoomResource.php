<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'type' => $this->type,
            'price' => $this->price,
            'area' => $this->area,
            'floor' => $this->floor,
            'status' => $this->status,
            'description' => $this->description,
            'photos' => $this->whenLoaded('photos', fn() =>
                $this->photos->map(fn($p) => ['url' => $p->url, 'is_primary' => $p->is_primary])
            ),
            'facilities' => $this->whenLoaded('facilities', fn() =>
                $this->facilities->map(fn($f) => ['id' => $f->id, 'name' => $f->name])
            ),
        ];
    }
}

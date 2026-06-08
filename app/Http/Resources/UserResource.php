<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'role'              => $this->role,
            'phone'             => $this->phone,
            'whatsapp_number'   => $this->whatsapp_number,
            'profile_photo_url' => $this->profile_photo_url,
            'status'            => $this->status,
            'tenant'            => $this->whenLoaded('tenant', function () {
                $rental = $this->tenant->activeRental;
                $room   = $rental?->room;

                $contractDuration = null;
                if ($rental?->start_date && $rental?->end_date) {
                    $contractDuration = (int) Carbon::parse($rental->start_date)
                        ->diffInMonths(Carbon::parse($rental->end_date));
                }

                return [
                    'id'              => $this->tenant->id,
                    'full_name'       => $this->tenant->full_name,
                    'nik'             => $this->tenant->nik,
                    'whatsapp_number' => $this->tenant->whatsapp_number,
                    'address'         => $this->tenant->address ?? '',
                    'status'          => $this->tenant->status,
                    'active_rental'   => $rental ? [
                        'id'                => $rental->id,
                        'start_date'        => $rental->start_date?->format('Y-m-d'),
                        'end_date'          => $rental->end_date?->format('Y-m-d'),
                        'contract_duration' => $contractDuration,
                        'monthly_rent'      => (float) $rental->monthly_rent,
                        'room'              => $room ? [
                            'id'          => $room->id,
                            'room_number' => $room->room_number,
                            'type'        => $room->type,
                            'price'       => (float) $room->price,
                            'description' => $room->description,
                            'status'      => $room->status,
                            'facilities'  => $room->facilities?->pluck('name')->implode(', ') ?? '',
                        ] : null,
                    ] : null,
                ];
            }),
        ];
    }
}

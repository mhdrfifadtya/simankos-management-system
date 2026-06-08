<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'period' => $this->period_label,
            'year' => $this->year,
            'month' => $this->month,
            'rent_amount' => $this->rent_amount,
            'extra_charges' => $this->extra_charges,
            'total' => $this->total,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'paid_date' => $this->paid_date?->format('Y-m-d'),
            'status' => $this->status,
            'status_color' => $this->status_badge_color,
            'room' => $this->whenLoaded('rental', fn() => $this->rental?->room ? [
                'room_number' => $this->rental->room->room_number,
                'type' => $this->rental->room->type,
            ] : null),
            'payments_count' => $this->whenLoaded('payments', fn() => $this->payments->count()),
            'paid_amount' => $this->whenLoaded('payments', fn() => $this->payments->where('status', 'lunas')->sum('amount')),
        ];
    }
}

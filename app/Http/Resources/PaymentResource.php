<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'payment_method' => $this->payment_method,
            'proof_url' => $this->proof_url,
            'status' => $this->status,
            'notes' => $this->notes,
            'verified_at' => $this->verified_at?->format('Y-m-d H:i'),
            'invoice' => $this->whenLoaded('invoice', fn() => [
                'invoice_number' => $this->invoice->invoice_number,
                'period' => $this->invoice->period_label,
                'total' => $this->invoice->total,
            ]),
        ];
    }
}

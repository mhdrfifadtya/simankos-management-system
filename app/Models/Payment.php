<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id', 'tenant_id', 'room_id',
        'amount', 'payment_date', 'payment_method',
        'proof_of_payment', 'status', 'notes',
        'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'verified_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProofUrlAttribute(): ?string
    {
        if (!$this->proof_of_payment) return null;
        if (file_exists(public_path("uploads/{$this->proof_of_payment}"))) {
            return asset("uploads/{$this->proof_of_payment}");
        }
        return asset("storage/{$this->proof_of_payment}");
    }

    public function isPending(): bool
    {
        return $this->status === 'menunggu_konfirmasi';
    }
}

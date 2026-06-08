<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Carbon\Carbon $due_date
 * @property \Carbon\Carbon|null $paid_date
 */
class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number', 'rental_id', 'tenant_id',
        'year', 'month', 'rent_amount', 'extra_charges',
        'total', 'due_date', 'paid_date', 'status',
        'snap_token', 'midtrans_order_id', 'midtrans_transaction_id',
        'payment_gateway',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_date' => 'date',
            'rent_amount' => 'decimal:2',
            'extra_charges' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'overdue' => 'red',
            'pending' => 'yellow',
            'partial' => 'orange',
            'sent' => 'blue',
            'draft' => 'gray',
            default => 'gray',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return ($months[$this->month] ?? $this->month) . ' ' . $this->year;
    }
}

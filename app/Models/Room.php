<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = [
        'room_number', 'type', 'price',
        'area', 'floor', 'status', 'description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area' => 'decimal:2',
        ];
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'room_facilities');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(RoomPhoto::class)->where('is_primary', true);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function activeRental(): HasOne
    {
        return $this->hasOne(Rental::class)->where('status', 'aktif');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'green',
            'terisi' => 'blue',
            'maintenance' => 'yellow',
            default => 'gray',
        };
    }

    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'full_name', 'nik', 'whatsapp_number',
        'address', 'emergency_contact', 'emergency_phone',
        'notify_via_whatsapp', 'notify_via_email',
        'id_card_photo', 'profile_photo', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'notify_via_whatsapp' => 'boolean',
            'notify_via_email' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function activeRental(): HasOne
    {
        return $this->hasOne(Rental::class)->where('status', 'aktif')->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function getIdCardPhotoUrlAttribute(): ?string
    {
        return $this->resolveUrl($this->id_card_photo);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->resolveUrl($this->profile_photo);
    }

    private function resolveUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (file_exists(public_path("uploads/{$path}"))) {
            return asset("uploads/{$path}");
        }
        return asset("storage/{$path}");
    }
}

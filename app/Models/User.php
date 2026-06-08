<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'whatsapp_number', 'address', 'ktp_number',
        'ktp_photo', 'profile_photo', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPenghuni(): bool
    {
        return $this->hasRole('penghuni');
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) return null;
        if (file_exists(public_path("uploads/{$this->profile_photo}"))) {
            return asset("uploads/{$this->profile_photo}");
        }
        return asset("storage/{$this->profile_photo}");
    }
}

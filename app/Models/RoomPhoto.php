<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPhoto extends Model
{
    protected $fillable = ['room_id', 'photo_path', 'is_primary'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getUrlAttribute(): string
    {
        // Cek di public/uploads terlebih dahulu (disk baru), fallback ke storage symlink
        if ($this->photo_path && file_exists(public_path('uploads/' . $this->photo_path))) {
            return asset('uploads/' . $this->photo_path);
        }
        return asset('storage/' . $this->photo_path);
    }
}

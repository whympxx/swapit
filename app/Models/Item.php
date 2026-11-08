<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    public const CATEGORIES = ['Buku', 'Elektronik', 'Pakaian', 'Alat Olahraga', 'Peralatan Kampus', 'Lainnya'];
    public const CONDITIONS = ['Baru', 'Seperti Baru', 'Baik', 'Layak Pakai'];
    public const STATUS_LABELS = [
        'available' => 'Tersedia',
        'pending' => 'Dalam Proses',
        'traded' => 'Sudah Ditukar',
    ];

    protected $fillable = ['user_id', 'title', 'description', 'category', 'condition_item', 'photo', 'status'];

    public const CATEGORY_ICONS = [
        'Buku' => 'book-open',
        'Elektronik' => 'laptop',
        'Pakaian' => 'shirt',
        'Alat Olahraga' => 'basketball',
        'Peralatan Kampus' => 'graduation-cap',
        'Lainnya' => 'gem',
    ];

    protected function casts(): array
    {
        return ['user_id' => 'integer', 'views' => 'integer'];
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function getCategoryIconAttribute(): string
    {
        return self::CATEGORY_ICONS[$this->category] ?? 'tag';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomingOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'item_wanted_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function getPhotoUrlAttribute(): string
    {
        return asset('uploads/items/' . $this->photo);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}

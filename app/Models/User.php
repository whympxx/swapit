<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['username', 'email', 'password', 'full_name', 'phone', 'university'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /** ['avg' => 4.5, 'count' => 2] (avg null jika belum ada ulasan) */
    public function ratingSummary(): array
    {
        $row = $this->reviewsReceived()->selectRaw('AVG(rating) AS avg_rating, COUNT(*) AS cnt')->first();
        $count = (int) ($row->cnt ?? 0);

        return ['avg' => $count ? round((float) $row->avg_rating, 1) : null, 'count' => $count];
    }

    /** Inisial nama untuk avatar, mis. "Wahyu Marga" -> "WM" */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->full_name ?: $this->username)) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= mb_strtoupper(mb_substr($w, 0, 1));
        }
        return $initials ?: '?';
    }

    /** Warna avatar yang konsisten per pengguna (hue 0-359) */
    public function getAvatarHueAttribute(): int
    {
        return crc32((string) $this->username) % 360;
    }

    public function completedTradesCount(): int
    {
        return Offer::where('status', Offer::COMPLETED)->involving($this->id)->count();
    }

    /** Nomor HP Indonesia (08xx / +628xx) -> format wa.me */
    public function whatsappNumber(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }
        return strlen($digits) >= 9 ? $digits : null;
    }
}

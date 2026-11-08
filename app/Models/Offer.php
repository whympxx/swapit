<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    public const PENDING = 'pending';
    public const ACCEPTED = 'accepted';
    public const REJECTED = 'rejected';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::PENDING => 'Menunggu',
        self::ACCEPTED => 'Diterima',
        self::REJECTED => 'Ditolak',
        self::COMPLETED => 'Selesai',
        self::CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = ['sender_id', 'receiver_id', 'item_wanted_id', 'item_offered_id', 'message', 'status'];

    protected function casts(): array
    {
        return [
            'sender_id' => 'integer',
            'receiver_id' => 'integer',
            'item_wanted_id' => 'integer',
            'item_offered_id' => 'integer',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function wantedItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_wanted_id');
    }

    public function offeredItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_offered_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** User boleh memberi ulasan: barter selesai, ia terlibat, dan belum pernah mengulas */
    public function canBeReviewedBy(?User $user): bool
    {
        if (!$user || $this->status !== self::COMPLETED || !$this->involves($user)) {
            return false;
        }
        if ($this->relationLoaded('reviews')) {
            return !$this->reviews->contains('reviewer_id', $user->id);
        }
        return !$this->reviews()->where('reviewer_id', $user->id)->exists();
    }

    public function scopeInvolving($query, int $userId)
    {
        return $query->where(fn ($q) => $q->where('sender_id', $userId)->orWhere('receiver_id', $userId));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function involves(?User $user): bool
    {
        return $user && in_array($user->id, [$this->sender_id, $this->receiver_id], true);
    }

    public function otherParty(User $user): User
    {
        return $user->id === $this->sender_id ? $this->receiver : $this->sender;
    }

    public function dealLabel(): string
    {
        return '"' . $this->offeredItem->title . '" ⇄ "' . $this->wantedItem->title . '"';
    }
}

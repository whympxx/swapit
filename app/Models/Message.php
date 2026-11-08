<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['offer_id', 'sender_id', 'receiver_id', 'message', 'is_read'];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'offer_id' => 'integer',
            'sender_id' => 'integer',
            'receiver_id' => 'integer',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function toChatArray(int $currentUserId): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'message' => $this->message,
            'full_name' => $this->sender->full_name,
            'created_at' => $this->created_at?->toDateTimeString(),
            'time_ago' => $this->created_at?->diffForHumans(),
            'is_mine' => $this->sender_id === $currentUserId,
        ];
    }
}

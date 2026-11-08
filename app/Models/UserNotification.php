<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Notifikasi di dalam aplikasi (tabel "notifications" dari versi lama). */
class UserNotification extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'notifications';

    protected $fillable = ['user_id', 'title', 'message', 'link', 'is_read'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean', 'user_id' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** $link: path relatif aplikasi, mis. "offers" atau "chat/5" */
    public static function send(int $userId, string $title, string $message, ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    /** Link dari versi lama tersimpan sebagai "pages/xxx.php?..."; dipetakan ke route baru. */
    public function targetUrl(): string
    {
        $link = (string) $this->link;
        if ($link === '' || preg_match('#^(https?:)?//#i', $link)) {
            return route('notifications.index');
        }
        if (preg_match('#chat\.php\?offer_id=(\d+)#', $link, $m)) {
            return route('chat.show', $m[1]);
        }
        if (str_contains($link, 'my-offers.php')) {
            return route('offers.index');
        }
        return url(ltrim($link, '/'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'item_id'];

    protected function casts(): array
    {
        return ['user_id' => 'integer', 'item_id' => 'integer'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

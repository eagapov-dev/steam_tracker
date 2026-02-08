<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Watchlist extends Pivot
{
    protected $table = 'watchlist';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'game_id',
        'target_price',
        'notify_any_discount',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'target_price' => 'decimal:2',
            'notify_any_discount' => 'boolean',
            'notified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}

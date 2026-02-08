<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'steam_app_id',
        'name',
        'header_image_url',
        'current_price',
        'current_discount_percent',
        'is_free',
        'price_last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'steam_app_id' => 'integer',
            'current_price' => 'decimal:2',
            'current_discount_percent' => 'integer',
            'is_free' => 'boolean',
            'price_last_checked_at' => 'datetime',
        ];
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(PriceHistory::class)->orderByDesc('recorded_at');
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'watchlist')
            ->using(Watchlist::class)
            ->withPivot(['target_price', 'notify_any_discount', 'notified_at'])
            ->withTimestamps();
    }

    public function watchlistEntries(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function currentDiscount(): int
    {
        return $this->current_discount_percent ?? 0;
    }

    public function scopeBySteamAppId($query, int $steamAppId)
    {
        return $query->where('steam_app_id', $steamAppId);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'lemon_squeezy_customer_id',
        'subscription_status',
        'subscription_ends_at',
        'telegram_chat_id',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    public function watchlist(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'watchlist')
            ->using(Watchlist::class)
            ->withPivot(['target_price', 'notify_any_discount', 'notified_at'])
            ->withTimestamps();
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function notificationsLog(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function planConfig(): array
    {
        return config("plans.{$this->plan}", config('plans.free'));
    }

    public function gameLimit(): ?int
    {
        return $this->planConfig()['game_limit'];
    }

    public function canAddGame(): bool
    {
        $limit = $this->gameLimit();
        if ($limit === null) {
            return true;
        }

        return $this->watchlistItems()->count() < $limit;
    }

    public function isSubscribed(): bool
    {
        return $this->plan !== 'free' && $this->subscription_status === 'active';
    }

    public function hasFeature(string $feature): bool
    {
        return (bool) ($this->planConfig()[$feature] ?? false);
    }

    public function priceHistoryDays(): ?int
    {
        return $this->planConfig()['price_history_days'];
    }
}

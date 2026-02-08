<?php

namespace App\Notifications;

use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceDropNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Game $game,
        private float $oldPrice,
        private float $newPrice,
        private int $discountPercent
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $savings = number_format($this->oldPrice - $this->newPrice, 2);

        return (new MailMessage)
            ->subject("Price Drop: {$this->game->name} is now \${$this->newPrice}!")
            ->greeting("Great news, {$notifiable->name}!")
            ->line("{$this->game->name} just dropped in price.")
            ->line("**Old price:** \${$this->oldPrice}")
            ->line("**New price:** \${$this->newPrice} ({$this->discountPercent}% off)")
            ->line("**You save:** \${$savings}")
            ->action('View on Steam', "https://store.steampowered.com/app/{$this->game->steam_app_id}")
            ->line('Happy gaming!');
    }
}

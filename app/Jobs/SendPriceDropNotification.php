<?php

namespace App\Jobs;

use App\Models\Game;
use App\Models\NotificationLog;
use App\Models\User;
use App\Notifications\PriceDropNotification;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPriceDropNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private User $user,
        private Game $game,
        private float $oldPrice,
        private float $newPrice,
        private int $discountPercent
    ) {}

    public function handle(TelegramService $telegram): void
    {
        $planConfig = $this->user->planConfig();

        // Send email notification
        if ($planConfig['email_notifications']) {
            try {
                $this->user->notify(new PriceDropNotification(
                    $this->game,
                    $this->oldPrice,
                    $this->newPrice,
                    $this->discountPercent
                ));

                NotificationLog::create([
                    'user_id' => $this->user->id,
                    'game_id' => $this->game->id,
                    'channel' => 'email',
                    'type' => 'price_drop',
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send email notification', [
                    'user_id' => $this->user->id,
                    'game_id' => $this->game->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send Telegram notification
        if ($planConfig['telegram_notifications'] && $this->user->telegram_chat_id) {
            try {
                $message = "🎮 *Price Drop Alert!*\n\n"
                    ."*{$this->game->name}*\n"
                    ."~~\${$this->oldPrice}~~ → *\${$this->newPrice}*\n"
                    ."Discount: *{$this->discountPercent}%* off\n\n"
                    ."[View on Steam](https://store.steampowered.com/app/{$this->game->steam_app_id})";

                $telegram->sendMessage($this->user->telegram_chat_id, $message);

                NotificationLog::create([
                    'user_id' => $this->user->id,
                    'game_id' => $this->game->id,
                    'channel' => 'telegram',
                    'type' => 'price_drop',
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification', [
                    'user_id' => $this->user->id,
                    'game_id' => $this->game->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

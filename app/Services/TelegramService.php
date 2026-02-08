<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;

    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token');
        $this->apiUrl = config('telegram.api_url');
    }

    public function sendMessage(string $chatId, string $text, string $parseMode = 'Markdown'): bool
    {
        if (! $this->botToken) {
            Log::warning('Telegram bot token not configured.');

            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}{$this->botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => false,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram send failed', ['error' => $e->getMessage(), 'chat_id' => $chatId]);

            return false;
        }
    }

    public function getUpdates(int $offset = 0): array
    {
        if (! $this->botToken) {
            return [];
        }

        try {
            $response = Http::get("{$this->apiUrl}{$this->botToken}/getUpdates", [
                'offset' => $offset,
            ]);

            if ($response->successful()) {
                return $response->json('result', []);
            }
        } catch (\Exception $e) {
            Log::error('Telegram getUpdates failed', ['error' => $e->getMessage()]);
        }

        return [];
    }
}

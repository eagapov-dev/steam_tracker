<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleLemonSqueezy(Request $request)
    {
        $secret = config('lemonsqueezy.webhook_secret');
        $signature = $request->header('X-Signature');

        if (! $signature || ! $this->verifySignature($request->getContent(), $secret, $signature)) {
            abort(403, 'Invalid signature.');
        }

        $payload = $request->all();
        $eventName = $payload['meta']['event_name'] ?? null;
        $customData = $payload['meta']['custom_data'] ?? [];
        $userId = $customData['user_id'] ?? null;

        if (! $userId) {
            Log::warning('LemonSqueezy webhook: missing user_id', $payload);

            return response()->json(['message' => 'OK']);
        }

        $user = User::find($userId);
        if (! $user) {
            Log::warning('LemonSqueezy webhook: user not found', ['user_id' => $userId]);

            return response()->json(['message' => 'OK']);
        }

        match ($eventName) {
            'subscription_created' => $this->handleSubscriptionCreated($user, $payload),
            'subscription_updated' => $this->handleSubscriptionUpdated($user, $payload),
            'subscription_cancelled' => $this->handleSubscriptionCancelled($user, $payload),
            'subscription_expired' => $this->handleSubscriptionExpired($user, $payload),
            default => Log::info("LemonSqueezy webhook: unhandled event {$eventName}"),
        };

        return response()->json(['message' => 'OK']);
    }

    private function verifySignature(string $payload, string $secret, string $signature): bool
    {
        $computedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($computedSignature, $signature);
    }

    private function handleSubscriptionCreated(User $user, array $payload): void
    {
        $attributes = $payload['data']['attributes'] ?? [];
        $variantId = (string) ($attributes['variant_id'] ?? '');

        $plan = $this->resolvePlanFromVariant($variantId);

        $user->update([
            'plan' => $plan,
            'lemon_squeezy_customer_id' => $attributes['customer_id'] ?? null,
            'subscription_status' => 'active',
            'subscription_ends_at' => $attributes['renews_at'] ?? null,
        ]);
    }

    private function handleSubscriptionUpdated(User $user, array $payload): void
    {
        $attributes = $payload['data']['attributes'] ?? [];
        $variantId = (string) ($attributes['variant_id'] ?? '');
        $status = $attributes['status'] ?? 'active';

        $plan = $this->resolvePlanFromVariant($variantId);

        $user->update([
            'plan' => $plan,
            'subscription_status' => $status,
            'subscription_ends_at' => $attributes['renews_at'] ?? $attributes['ends_at'] ?? null,
        ]);
    }

    private function handleSubscriptionCancelled(User $user, array $payload): void
    {
        $attributes = $payload['data']['attributes'] ?? [];

        $user->update([
            'subscription_status' => 'cancelled',
            'subscription_ends_at' => $attributes['ends_at'] ?? null,
        ]);
    }

    private function handleSubscriptionExpired(User $user, array $payload): void
    {
        $user->update([
            'plan' => 'free',
            'subscription_status' => 'expired',
            'subscription_ends_at' => null,
        ]);
    }

    private function resolvePlanFromVariant(string $variantId): string
    {
        $variants = config('lemonsqueezy.variant_ids');

        foreach ($variants as $plan => $id) {
            if ((string) $id === $variantId) {
                return $plan;
            }
        }

        return 'free';
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    public function pricing()
    {
        $plans = config('plans');
        return view('pricing', compact('plans'));
    }

    public function checkout(string $plan, Request $request)
    {
        $variantId = config("lemonsqueezy.variant_ids.{$plan}");

        if (!$variantId) {
            abort(404, 'Invalid plan.');
        }

        $user = $request->user();

        $response = Http::withToken(config('lemonsqueezy.api_key'))
            ->post('https://api.lemonsqueezy.com/v1/checkouts', [
                'data' => [
                    'type' => 'checkouts',
                    'attributes' => [
                        'checkout_data' => [
                            'email' => $user->email,
                            'name' => $user->name,
                            'custom' => [
                                'user_id' => (string) $user->id,
                            ],
                        ],
                    ],
                    'relationships' => [
                        'store' => [
                            'data' => [
                                'type' => 'stores',
                                'id' => config('lemonsqueezy.store_id'),
                            ],
                        ],
                        'variant' => [
                            'data' => [
                                'type' => 'variants',
                                'id' => $variantId,
                            ],
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            return back()->with('error', 'Unable to create checkout. Please try again.');
        }

        $checkoutUrl = $response->json('data.attributes.url');

        return redirect($checkoutUrl);
    }

    public function portal(Request $request)
    {
        $user = $request->user();

        if (!$user->lemon_squeezy_customer_id) {
            return redirect()->route('pricing')->with('info', 'No active subscription found.');
        }

        return view('subscription.portal', compact('user'));
    }
}

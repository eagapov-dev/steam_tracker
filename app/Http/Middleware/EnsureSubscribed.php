<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next, ?string $plan = null): Response
    {
        $user = $request->user();

        if (! $user || $user->plan === 'free') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Subscription required.'], 403);
            }

            return redirect()->route('pricing')->with('error', 'You need an active subscription to access this feature.');
        }

        if ($plan && $user->plan !== $plan) {
            $planNames = ['starter' => 1, 'pro' => 2, 'enterprise' => 3];
            $requiredLevel = $planNames[$plan] ?? 0;
            $userLevel = $planNames[$user->plan] ?? 0;

            if ($userLevel < $requiredLevel) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => "This feature requires the {$plan} plan or higher."], 403);
                }

                return redirect()->route('pricing')->with('error', "This feature requires the {$plan} plan or higher.");
            }
        }

        return $next($request);
    }
}

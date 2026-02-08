<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGameLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->canAddGame()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Game limit reached. Upgrade your plan to track more games.',
                ], 403);
            }

            return back()->with('error', "You've reached your limit of {$user->gameLimit()} games. Upgrade your plan to track more.");
        }

        return $next($request);
    }
}

<?php

namespace App\Shared\Middleware;

use App\Modules\Subscription\Models\Subscription;
use App\Modules\User\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionStatusMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        if ($user->isOwner()) {
            return $next($request);
        }

        $tenant = $user->tenant;
        if (!$tenant) {
            return $next($request);
        }

        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if (!$subscription) {
            return $next($request);
        }

        if (in_array($subscription->status, ['suspended', 'expired', 'cancelled'], true)) {
            return $this->blocked('subscription_' . $subscription->status);
        }

        return $next($request);
    }

    protected function blocked(string $reason): JsonResponse
    {
        return response()->json([
            'message' => 'Subscription inactive',
            'reason' => $reason,
        ], 402);
    }
}

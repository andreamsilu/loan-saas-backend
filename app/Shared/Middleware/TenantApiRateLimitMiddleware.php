<?php

namespace App\Shared\Middleware;

use App\Modules\Subscription\Models\Subscription;
use App\Shared\Services\TenantManager;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TenantApiRateLimitMiddleware
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function handle(Request $request, Closure $next)
    {
        $tenantId = $this->tenantManager->getTenantId();

        if (!$tenantId) {
            return $next($request);
        }

        $subscription = Subscription::where('tenant_id', $tenantId)->latest()->with('plan')->first();
        $plan = $subscription?->plan;
        $maxPerMinute = $plan?->api_limit_per_minute ?? 60;

        $key = 'tenant_api:' . $tenantId;

        if (RateLimiter::tooManyAttempts($key, $maxPerMinute)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->limited($seconds);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }

    protected function limited(int $retryAfter): JsonResponse
    {
        return response()->json([
            'message' => 'Too many requests for current plan',
            'retry_after_seconds' => $retryAfter,
        ], 429);
    }
}


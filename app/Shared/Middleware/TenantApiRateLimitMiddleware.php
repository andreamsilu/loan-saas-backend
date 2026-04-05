<?php

namespace App\Shared\Middleware;

use App\Shared\Services\TenantManager;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TenantApiRateLimitMiddleware
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $tenantId = $this->tenantManager->getTenantId();

        if (!$tenantId) {
            return $next($request);
        }

        $maxPerMinute = max(1, (int) config('api.tenant_rate_limit_per_minute', 120));

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
            'message' => 'Too many requests',
            'retry_after_seconds' => $retryAfter,
        ], 429);
    }
}

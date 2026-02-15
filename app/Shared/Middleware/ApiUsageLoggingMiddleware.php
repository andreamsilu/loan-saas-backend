<?php

namespace App\Shared\Middleware;

use App\Modules\Developer\Models\ApiKey;
use App\Modules\Developer\Models\ApiUsageLog;
use App\Shared\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;

class ApiUsageLoggingMiddleware
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $tenantId = $this->tenantManager->getTenantId();
        if ($tenantId) {
            $apiKeyId = null;
            if ($request->hasHeader('X-Api-Key')) {
                $token = $request->header('X-Api-Key');
                $hash = hash('sha256', $token);
                $key = ApiKey::where('token_hash', $hash)->first();
                if ($key && $key->tenant_id === $tenantId) {
                    $apiKeyId = $key->id;
                }
            }

            $duration = (int) ((microtime(true) - $start) * 1000);

            ApiUsageLog::create([
                'tenant_id' => $tenantId,
                'api_key_id' => $apiKeyId,
                'method' => $request->getMethod(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'occurred_at' => now(),
            ]);
        }

        return $response;
    }
}


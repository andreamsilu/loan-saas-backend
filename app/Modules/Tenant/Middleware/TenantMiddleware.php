<?php

namespace App\Modules\Tenant\Middleware;

use App\Modules\Tenant\Models\Tenant;
use App\Shared\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // 1. Resolve from X-Tenant-ID header
        if ($request->hasHeader('X-Tenant-ID')) {
            $tenant = Tenant::find($request->header('X-Tenant-ID'));
        }

        // 2. Resolve from subdomain if not found
        if (!$tenant) {
            $host = $request->getHost();
            $subdomain = explode('.', $host)[0];
            $tenant = Tenant::where('subdomain', $subdomain)->first();
        }

        if (!$tenant || !$tenant->is_active) {
            return response()->json(['message' => 'Tenant not found or inactive.'], 404);
        }

        $this->tenantManager->setTenant($tenant);

        return $next($request);
    }
}

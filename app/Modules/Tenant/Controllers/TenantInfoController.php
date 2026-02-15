<?php

namespace App\Modules\Tenant\Controllers;

use App\Http\Controllers\Controller;
use App\Shared\Services\TenantManager;

class TenantInfoController extends Controller
{
    public function me(TenantManager $tenantManager)
    {
        $tenant = $tenantManager->getTenant();
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not resolved'], 404);
        }
        return response()->json(['tenant' => $tenant]);
    }
}


<?php

namespace App\Shared\Services\Sms;

use App\Shared\Services\TenantManager;

class TenantSmsConfigService
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    public function forCurrentTenant(): ?array
    {
        $tenant = $this->tenantManager->getTenant();
        if (!$tenant) {
            return null;
        }

        $settings = $tenant->settings ?? [];
        $sms = $settings['sms'] ?? null;
        if (!$sms || !is_array($sms)) {
            return null;
        }

        return $sms;
    }
}


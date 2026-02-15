<?php

namespace App\Shared\Services\Payment;

use App\Shared\Services\TenantManager;

class PaymentConfigService
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
        $payments = $settings['payments'] ?? null;
        if (!$payments || !is_array($payments)) {
            return null;
        }

        return $payments;
    }
}


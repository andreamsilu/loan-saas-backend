<?php

namespace App\Modules\Audit\Services;

use App\Modules\Audit\Models\AuditLog;

class AuditService
{
    public function record(string $action, array $context = []): void
    {
        $user = auth()->user();
        $tenantId = $user?->tenant?->id ?? null;
        $data = [
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $context['entity_type'] ?? null,
            'entity_id' => $context['entity_id'] ?? null,
            'ip_address' => request()?->ip(),
            'old_values' => $context['old'] ?? null,
            'new_values' => $context['new'] ?? null,
        ];
        AuditLog::create($data);
    }
}


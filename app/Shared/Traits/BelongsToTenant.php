<?php

namespace App\Shared\Traits;

use App\Modules\Tenant\Models\Tenant;
use App\Shared\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        $tenantManager = App::make(TenantManager::class);

        static::creating(function ($model) use ($tenantManager) {
            if (!$model->tenant_id && $tenantManager->getTenantId()) {
                $model->tenant_id = $tenantManager->getTenantId();
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) use ($tenantManager) {
            if ($tenantManager->getTenantId()) {
                $builder->where('tenant_id', $tenantManager->getTenantId());
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}

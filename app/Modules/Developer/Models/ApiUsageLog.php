<?php

namespace App\Modules\Developer\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiUsageLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'api_key_id',
        'method',
        'path',
        'status',
        'duration_ms',
        'ip',
        'user_agent',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}


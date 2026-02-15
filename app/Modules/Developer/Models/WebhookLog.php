<?php

namespace App\Modules\Developer\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'webhook_endpoint_id',
        'event',
        'payload',
        'response_status',
        'response_body',
        'attempts',
        'last_attempt_at',
        'success',
    ];

    protected $casts = [
        'payload' => 'array',
        'last_attempt_at' => 'datetime',
        'success' => 'boolean',
    ];
}


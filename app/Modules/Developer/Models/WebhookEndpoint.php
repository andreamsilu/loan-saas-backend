<?php

namespace App\Modules\Developer\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'url',
        'events',
        'active',
        'secret',
        'last_used_at',
    ];

    protected $casts = [
        'events' => 'array',
        'active' => 'boolean',
        'last_used_at' => 'datetime',
    ];
}


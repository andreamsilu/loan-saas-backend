<?php

namespace App\Modules\Developer\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'token_hash',
        'scopes',
        'active',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public static function createWithToken(array $attributes): array
    {
        $token = Str::random(48);
        $attributes['token_hash'] = hash('sha256', $token);
        $key = static::create($attributes);
        return [$key, $token];
    }
}


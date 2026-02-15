<?php

namespace App\Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'domain',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function subscription()
    {
        return $this->hasOne(\App\Modules\Subscription\Models\Subscription::class);
    }
}

<?php

namespace App\Modules\Subscription\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'billing_cycle',
        'max_staff',
        'max_borrowers',
        'max_loans_per_month',
        'api_limit_per_minute',
        'features',
        'price',
        'support_level',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];
}


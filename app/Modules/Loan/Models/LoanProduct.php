<?php

namespace App\Modules\Loan\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'interest_calculation_type',
        'interest_rate',
        'term_duration',
        'term_period',
        'min_amount',
        'max_amount',
        'processing_fee',
        'processing_fee_type',
        'grace_period_days',
        'repayment_frequency',
        'penalty_rate',
        'penalty_type',
    ];

    protected $casts = [
        'interest_rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'penalty_rate' => 'decimal:2',
    ];
}

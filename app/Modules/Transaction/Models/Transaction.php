<?php

namespace App\Modules\Transaction\Models;

use App\Shared\Traits\BelongsToTenant;
use App\Modules\Loan\Models\Loan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'loan_id',
        'transaction_number',
        'amount',
        'type',
        'payment_method',
        'reference',
        'metadata',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'transaction_date' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}

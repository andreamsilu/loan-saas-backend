<?php

namespace App\Modules\Loan\Models;

use App\Shared\Traits\BelongsToTenant;
use App\Modules\Borrower\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'borrower_id',
        'amount',
        'interest_rate',
        'duration_months',
        'status',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }
}

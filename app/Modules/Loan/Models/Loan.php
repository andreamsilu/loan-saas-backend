<?php

namespace App\Modules\Loan\Models;

use App\Shared\Traits\BelongsToTenant;
use App\Modules\Borrower\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Modules\Loan\Models\LoanProduct;

class Loan extends Model
{
    use HasFactory, BelongsToTenant;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DISBURSED = 'disbursed';
    const STATUS_ACTIVE = 'active';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CLOSED = 'closed';
    const STATUS_DEFAULTED = 'defaulted';

    protected $fillable = [
        'tenant_id',
        'loan_product_id',
        'borrower_id',
        'loan_number',
        'amount',
        'interest_rate',
        'duration_months',
        'status',
        'application_date',
        'approval_date',
        'disbursement_date',
        'maturity_date',
        'total_payable',
        'total_paid',
        'repayment_schedule',
    ];

    protected $casts = [
        'repayment_schedule' => 'array',
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'application_date' => 'date',
        'approval_date' => 'date',
        'disbursement_date' => 'date',
        'maturity_date' => 'date',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }
}

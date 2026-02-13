<?php

namespace App\Modules\Loan\Events;

use App\Modules\Loan\Models\Loan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RepaymentReceived
{
    use Dispatchable, SerializesModels;

    public $loan;
    public $amount;

    public function __construct(Loan $loan, float $amount)
    {
        $this->loan = $loan;
        $this->amount = $amount;
    }
}

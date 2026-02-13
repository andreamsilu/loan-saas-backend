<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Loan\Events\LoanApproved;
use App\Modules\Loan\Events\LoanDisbursed;
use App\Modules\Loan\Events\RepaymentReceived;
use Illuminate\Support\Facades\Log;

class LoanNotificationListener
{
    public function handleLoanApproved(LoanApproved $event)
    {
        Log::info("Notification: Loan {$event->loan->loan_number} approved. Sending SMS/Email to borrower {$event->loan->borrower_id}.");
        // In a real app, you would use $event->loan->borrower->notify(...)
    }

    public function handleLoanDisbursed(LoanDisbursed $event)
    {
        Log::info("Notification: Loan {$event->loan->loan_number} disbursed. Sending SMS/Email to borrower {$event->loan->borrower_id}.");
    }

    public function handleRepaymentReceived(RepaymentReceived $event)
    {
        Log::info("Notification: Repayment of {$event->amount} received for loan {$event->loan->loan_number}. Sending receipt to borrower {$event->loan->borrower_id}.");
    }

    public function subscribe($events)
    {
        return [
            LoanApproved::class => 'handleLoanApproved',
            LoanDisbursed::class => 'handleLoanDisbursed',
            RepaymentReceived::class => 'handleRepaymentReceived',
        ];
    }
}

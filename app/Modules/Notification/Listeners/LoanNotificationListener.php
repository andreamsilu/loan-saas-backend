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

        $borrower = $event->loan->borrower;
        if ($borrower) {
            if ($borrower->phone) {
                try {
                    $sms = app(\App\Shared\Interfaces\SmsGatewayInterface::class);
                    $sms->send($borrower->phone, "Your loan {$event->loan->loan_number} has been approved.");
                } catch (\Throwable $e) {
                    Log::error('SMS send failed for loan approved: ' . $e->getMessage());
                }
            }
            if ($borrower->email) {
                try {
                    $email = app(\App\Shared\Interfaces\EmailSenderInterface::class);
                    $email->send($borrower->email, 'Loan approved', "Your loan {$event->loan->loan_number} has been approved.");
                } catch (\Throwable $e) {
                    Log::error('Email send failed for loan approved: ' . $e->getMessage());
                }
            }
        }
    }

    public function handleLoanDisbursed(LoanDisbursed $event)
    {
        Log::info("Notification: Loan {$event->loan->loan_number} disbursed. Sending SMS/Email to borrower {$event->loan->borrower_id}.");

        $borrower = $event->loan->borrower;
        if ($borrower) {
            if ($borrower->phone) {
                try {
                    $sms = app(\App\Shared\Interfaces\SmsGatewayInterface::class);
                    $sms->send($borrower->phone, "Your loan {$event->loan->loan_number} has been disbursed.");
                } catch (\Throwable $e) {
                    Log::error('SMS send failed for loan disbursed: ' . $e->getMessage());
                }
            }
            if ($borrower->email) {
                try {
                    $email = app(\App\Shared\Interfaces\EmailSenderInterface::class);
                    $email->send($borrower->email, 'Loan disbursed', "Your loan {$event->loan->loan_number} has been disbursed.");
                } catch (\Throwable $e) {
                    Log::error('Email send failed for loan disbursed: ' . $e->getMessage());
                }
            }
        }
    }

    public function handleRepaymentReceived(RepaymentReceived $event)
    {
        Log::info("Notification: Repayment of {$event->amount} received for loan {$event->loan->loan_number}. Sending receipt to borrower {$event->loan->borrower_id}.");

        $borrower = $event->loan->borrower;
        if ($borrower) {
            if ($borrower->phone) {
                try {
                    $sms = app(\App\Shared\Interfaces\SmsGatewayInterface::class);
                    $sms->send($borrower->phone, "Payment of {$event->amount} received for loan {$event->loan->loan_number}.");
                } catch (\Throwable $e) {
                    Log::error('SMS send failed for repayment received: ' . $e->getMessage());
                }
            }
            if ($borrower->email) {
                try {
                    $email = app(\App\Shared\Interfaces\EmailSenderInterface::class);
                    $email->send($borrower->email, 'Repayment received', "Payment of {$event->amount} received for loan {$event->loan->loan_number}.");
                } catch (\Throwable $e) {
                    Log::error('Email send failed for repayment received: ' . $e->getMessage());
                }
            }
        }
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

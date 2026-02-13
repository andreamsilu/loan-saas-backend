<?php

namespace App\Modules\Loan\Services;

use App\Modules\Loan\Models\Loan;
use App\Modules\Transaction\Services\TransactionService;
use App\Shared\Services\Payment\PaymentGatewayFactory;
use App\Modules\Tenant\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Modules\Loan\Events\RepaymentReceived;

class RepaymentService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function processRepayment(Loan $loan, float $amount, array $paymentDetails)
    {
        if ($loan->status !== Loan::STATUS_DISBURSED && $loan->status !== Loan::STATUS_ACTIVE && $loan->status !== Loan::STATUS_OVERDUE) {
            throw new Exception("Loan is not in a payable state.");
        }

        $tenant = auth()->user()->tenant;
        $gatewayName = $tenant->settings['payment_gateway'] ?? 'stripe';
        $gateway = PaymentGatewayFactory::make($gatewayName);

        return DB::transaction(function () use ($loan, $amount, $paymentDetails, $gateway) {
            $result = $gateway->process($amount, $paymentDetails);

            if (!$result['success']) {
                throw new Exception("Payment failed: " . $result['message']);
            }

            // Record transaction
            $this->transactionService->recordTransaction([
                'loan_id' => $loan->id,
                'amount' => $amount,
                'type' => 'repayment',
                'payment_method' => $gatewayName,
                'reference' => $result['reference'],
                'transaction_date' => now(),
            ]);

            // Update loan total paid
            $loan->increment('total_paid', $amount);

            // Check if loan is fully paid
            if ($loan->total_paid >= $loan->total_payable) {
                $loan->update(['status' => Loan::STATUS_CLOSED]);
            } elseif ($loan->status === Loan::STATUS_DISBURSED) {
                $loan->update(['status' => Loan::STATUS_ACTIVE]);
            }

            // Update repayment schedule (mark installments as paid)
            $this->updateScheduleAfterPayment($loan, $amount);

            event(new RepaymentReceived($loan, $amount));

            return [
                'loan' => $loan,
                'transaction_reference' => $result['reference'],
            ];
        });
    }

    protected function updateScheduleAfterPayment(Loan $loan, float $amount)
    {
        $schedule = $loan->repayment_schedule;
        $remainingAmount = $amount;

        foreach ($schedule as &$installment) {
            if ($installment['status'] === 'pending' && $remainingAmount > 0) {
                if ($remainingAmount >= $installment['amount']) {
                    $installment['status'] = 'paid';
                    $remainingAmount -= $installment['amount'];
                } else {
                    // Partial payment logic could be more complex, 
                    // for now we just mark as partially_paid if we wanted, 
                    // but keeping it simple.
                    $installment['status'] = 'partially_paid';
                    $remainingAmount = 0;
                }
            }
        }

        $loan->update(['repayment_schedule' => $schedule]);
    }
}

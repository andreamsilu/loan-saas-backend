<?php

namespace App\Modules\Loan\Services;

use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\LoanProduct;
use App\Modules\Borrower\Models\Borrower;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Modules\Transaction\Services\TransactionService;
use App\Shared\Services\Payment\PaymentGatewayFactory;

use App\Modules\Loan\Events\LoanApproved;
use App\Modules\Loan\Events\LoanDisbursed;

class LoanService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function createLoanApplication(array $data)
    {
        $borrower = Borrower::findOrFail($data['borrower_id']);
        
        if ($borrower->isBlacklisted()) {
            throw new \Exception("Blacklisted borrower cannot apply for a new loan.");
        }

        $product = LoanProduct::findOrFail($data['loan_product_id']);

        // Validate amount
        if ($data['amount'] < $product->min_amount || $data['amount'] > $product->max_amount) {
            throw new \Exception("Loan amount must be between {$product->min_amount} and {$product->max_amount}.");
        }

        $loan = Loan::create([
            'borrower_id' => $borrower->id,
            'loan_product_id' => $product->id,
            'loan_number' => 'LN-' . strtoupper(Str::random(8)),
            'amount' => $data['amount'],
            'interest_rate' => $product->interest_rate,
            'duration_months' => $product->term_duration,
            'status' => Loan::STATUS_PENDING_APPROVAL,
            'application_date' => now(),
        ]);

        return $loan;
    }

    public function approveLoan(Loan $loan)
    {
        if ($loan->status !== Loan::STATUS_PENDING_APPROVAL) {
            throw new \Exception("Only pending loans can be approved.");
        }

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approval_date' => now(),
        ]);

        event(new LoanApproved($loan));

        return $loan;
    }

    public function disburseLoan(Loan $loan)
    {
        if ($loan->status !== Loan::STATUS_APPROVED) {
            throw new \Exception("Only approved loans can be disbursed.");
        }

        DB::transaction(function () use ($loan) {
            $tenant = auth()->user()->tenant;
            $gatewayName = $tenant->settings['payment_gateway'] ?? 'aggregator';
            $gateway = PaymentGatewayFactory::make($gatewayName);
            $payoutResult = $gateway->disburse($loan->amount, [
                'phone' => optional($loan->borrower)->phone,
                'reference' => $loan->loan_number,
                'description' => 'Loan disbursement',
            ]);
            if (!$payoutResult['success']) {
                throw new \Exception("Disbursement failed");
            }
            $schedule = $this->calculateRepaymentSchedule($loan);
            
            $loan->update([
                'status' => Loan::STATUS_DISBURSED,
                'disbursement_date' => now(),
                'repayment_schedule' => $schedule,
                'total_payable' => collect($schedule)->sum('amount'),
            ]);

            $this->transactionService->recordTransaction([
                'loan_id' => $loan->id,
                'amount' => $loan->amount,
                'type' => 'disbursement',
                'transaction_date' => now(),
            ]);

            event(new LoanDisbursed($loan));
        });

        return $loan;
    }

    protected function calculateRepaymentSchedule(Loan $loan)
    {
        $product = $loan->product;
        $installments = $loan->duration_months;
        $schedule = [];
        $totalInterest = ($loan->amount * ($loan->interest_rate / 100));
        $totalPayable = $loan->amount + $totalInterest;
        $installmentAmount = round($totalPayable / $installments, 2);

        for ($i = 1; $i <= $installments; $i++) {
            $schedule[] = [
                'installment' => $i,
                'due_date' => now()->addMonths($i)->toDateString(),
                'amount' => ($i === $installments) ? ($totalPayable - ($installmentAmount * ($installments - 1))) : $installmentAmount,
                'status' => 'pending',
            ];
        }

        return $schedule;
    }
}

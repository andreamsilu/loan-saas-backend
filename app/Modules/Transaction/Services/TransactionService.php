<?php

namespace App\Modules\Transaction\Services;

use App\Modules\Transaction\Models\Transaction;
use App\Modules\Loan\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function recordTransaction(array $data)
    {
        return Transaction::create([
            'tenant_id' => $data['tenant_id'] ?? auth()->user()->tenant_id,
            'loan_id' => $data['loan_id'],
            'transaction_number' => 'TRX-' . strtoupper(Str::random(10)),
            'amount' => $data['amount'],
            'type' => $data['type'],
            'payment_method' => $data['payment_method'] ?? null,
            'reference' => $data['reference'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'transaction_date' => $data['transaction_date'] ?? now(),
        ]);
    }

    public function getLoanTransactions(Loan $loan)
    {
        return Transaction::where('loan_id', $loan->id)->orderBy('transaction_date', 'desc')->get();
    }
}

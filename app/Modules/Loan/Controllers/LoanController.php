<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Services\LoanService;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'borrower_id' => 'required|exists:borrowers,id',
            'loan_product_id' => 'required|exists:loan_products,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $loan = $this->loanService->createLoanApplication($request->all());
            return response()->json($loan, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function approve(Loan $loan)
    {
        try {
            $updatedLoan = $this->loanService->approveLoan($loan);
            return response()->json($updatedLoan);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function disburse(Loan $loan)
    {
        try {
            $updatedLoan = $this->loanService->disburseLoan($loan);
            return response()->json($updatedLoan);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Loan $loan)
    {
        return response()->json($loan->load(['borrower', 'product']));
    }
}

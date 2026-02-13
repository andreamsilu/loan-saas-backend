<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Services\RepaymentService;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    protected $repaymentService;

    public function __construct(RepaymentService $repaymentService)
    {
        $this->repaymentService = $repaymentService;
    }

    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_details' => 'nullable|array',
        ]);

        try {
            $result = $this->repaymentService->processRepayment(
                $loan, 
                $request->amount, 
                $request->payment_details ?? []
            );

            return response()->json([
                'message' => 'Repayment processed successfully',
                'loan' => $result['loan'],
                'transaction_reference' => $result['transaction_reference'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}

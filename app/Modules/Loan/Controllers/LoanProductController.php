<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Loan\Models\LoanProduct;
use App\Modules\Loan\Services\LoanProductService;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;

class LoanProductController extends Controller
{
    protected $productService;

    public function __construct(LoanProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return response()->json($this->productService->getTenantProducts());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'interest_calculation_type' => 'required|in:flat,reducing_balance',
            'interest_rate' => 'required|numeric|min:0',
            'term_duration' => 'required|integer|min:1',
            'term_period' => 'required|in:days,weeks,months',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gte:min_amount',
            'processing_fee' => 'nullable|numeric|min:0',
            'processing_fee_type' => 'nullable|in:fixed,percentage',
            'grace_period_days' => 'nullable|integer|min:0',
            'repayment_frequency' => 'required|in:daily,weekly,monthly',
            'penalty_rate' => 'nullable|numeric|min:0',
            'penalty_type' => 'nullable|in:fixed,percentage',
        ]);

        $product = $this->productService->createProduct($request->all());

        return response()->json($product, 201);
    }

    public function show(LoanProduct $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, LoanProduct $product)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'interest_calculation_type' => 'sometimes|in:flat,reducing_balance',
            'interest_rate' => 'sometimes|numeric|min:0',
            'term_duration' => 'sometimes|integer|min:1',
            'term_period' => 'sometimes|in:days,weeks,months',
            'min_amount' => 'sometimes|numeric|min:0',
            'max_amount' => 'sometimes|numeric|gte:min_amount',
        ]);

        $updatedProduct = $this->productService->updateProduct($product, $request->all());

        return response()->json($updatedProduct);
    }

    public function destroy(LoanProduct $product)
    {
        try {
            $this->productService->deleteProduct($product);
            return response()->json(['message' => 'Loan product deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}

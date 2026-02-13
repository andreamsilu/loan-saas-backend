<?php

namespace App\Modules\Loan\Services;

use App\Modules\Loan\Models\LoanProduct;
use App\Modules\Loan\Models\Loan;
use Illuminate\Support\Facades\DB;

class LoanProductService
{
    public function createProduct(array $data)
    {
        return LoanProduct::create($data);
    }

    public function updateProduct(LoanProduct $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function deleteProduct(LoanProduct $product)
    {
        // Product cannot be deleted if loans exist (Requirement 71)
        $hasLoans = Loan::where('loan_product_id', $product->id)->exists();
        
        if ($hasLoans) {
            throw new \Exception("Cannot delete loan product because loans are associated with it.");
        }

        return $product->delete();
    }

    public function getTenantProducts()
    {
        return LoanProduct::all();
    }
}

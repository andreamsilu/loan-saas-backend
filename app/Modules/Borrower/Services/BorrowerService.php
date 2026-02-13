<?php

namespace App\Modules\Borrower\Services;

use App\Modules\Borrower\Models\Borrower;
use Illuminate\Support\Facades\Log;

class BorrowerService
{
    public function createBorrower(array $data)
    {
        $borrower = Borrower::create($data);
        Log::info("Borrower created: {$borrower->id} by user " . auth()->id());
        return $borrower;
    }

    public function updateBorrower(Borrower $borrower, array $data)
    {
        $borrower->update($data);
        Log::info("Borrower updated: {$borrower->id} by user " . auth()->id());
        return $borrower;
    }

    public function blacklistBorrower(Borrower $borrower)
    {
        $borrower->update(['status' => 'blacklisted']);
        Log::warning("Borrower blacklisted: {$borrower->id} by user " . auth()->id());
        return $borrower;
    }

    public function getTenantBorrowers()
    {
        return Borrower::all();
    }
}

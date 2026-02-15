<?php

namespace App\Modules\Borrower\Services;

use App\Modules\Borrower\Models\Borrower;
use Illuminate\Support\Facades\Log;
use App\Modules\Subscription\Services\PlanLimitService;

class BorrowerService
{
    protected $planLimitService;

    public function __construct(PlanLimitService $planLimitService)
    {
        $this->planLimitService = $planLimitService;
    }

    public function createBorrower(array $data)
    {
        $this->planLimitService->ensureCanCreateBorrower();
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

<?php

namespace App\Modules\Subscription\Services;

use App\Modules\Subscription\Models\Subscription;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Borrower\Models\Borrower;
use App\Modules\Loan\Models\Loan;

class PlanLimitService
{
    public function currentSubscription(): ?Subscription
    {
        $tenant = auth()->user()?->tenant;
        if (!$tenant) return null;
        return Subscription::where('tenant_id', $tenant->id)->latest()->first();
    }

    public function ensureCanCreateBorrower(): void
    {
        $sub = $this->currentSubscription();
        if (!$sub) return;
        if ($sub->status !== 'active' && $sub->status !== 'trial') {
            throw new \Exception('Subscription inactive');
        }
        $plan = $sub->plan;
        if ($plan) {
            $count = Borrower::count();
            if ($count >= $plan->max_borrowers) {
                throw new \Exception('Borrower limit reached for plan');
            }
        }
    }

    public function ensureCanCreateLoan(): void
    {
        $sub = $this->currentSubscription();
        if (!$sub) {
            return;
        }
        if ($sub->status !== 'active' && $sub->status !== 'trial') {
            throw new \Exception('Subscription inactive');
        }
        $plan = $sub->plan;
        if ($plan) {
            $monthStart = now()->startOfMonth();
            $count = Loan::where('created_at', '>=', $monthStart)->count();
            if ($count >= $plan->max_loans_per_month) {
                throw new \Exception('Monthly loan limit reached for plan');
            }
        }
    }
}


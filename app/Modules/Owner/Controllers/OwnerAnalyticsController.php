<?php

namespace App\Modules\Owner\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Loan\Models\Loan;
use App\Modules\Tenant\Models\Tenant;

class OwnerAnalyticsController extends Controller
{
    public function dashboard()
    {
        $tenantCount = Tenant::count();

        $loansTotal = Loan::withoutGlobalScope('tenant')->count();
        $activeLoans = Loan::withoutGlobalScope('tenant')
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])
            ->count();

        $totalSubscriptionRevenue = Invoice::withoutGlobalScope('tenant')
            ->where('status', 'paid')
            ->sum('amount');

        $monthlySubscriptionRevenue = Invoice::withoutGlobalScope('tenant')
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return response()->json([
            'tenants_total' => $tenantCount,
            'loans_total' => $loansTotal,
            'active_loans_total' => $activeLoans,
            'subscription_revenue_total' => $totalSubscriptionRevenue,
            'subscription_revenue_month' => $monthlySubscriptionRevenue,
        ]);
    }
}


<?php

namespace App\Modules\Report\Services;

use App\Modules\Loan\Models\Loan;
use App\Modules\Transaction\Models\Transaction;
use Illuminate\Support\Facades\DB;

class OperationalReportService
{
    public function getDashboardStats()
    {
        $stats = [
            'active_loans_count' => Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])->count(),
            'overdue_loans_count' => Loan::where('status', Loan::STATUS_OVERDUE)->count(),
            'total_disbursed' => Transaction::where('type', 'disbursement')->sum('amount'),
            'total_repaid' => Transaction::where('type', 'repayment')->sum('amount'),
            'total_portfolio' => Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED, Loan::STATUS_OVERDUE])->sum('amount'),
            'portfolio_at_risk_count' => Loan::where('status', Loan::STATUS_OVERDUE)->count(),
        ];

        // Calculate PAR percentage
        $stats['par_percentage'] = $stats['total_portfolio'] > 0 
            ? round((Loan::where('status', Loan::STATUS_OVERDUE)->sum('amount') / $stats['total_portfolio']) * 100, 2)
            : 0;

        $defaults = Loan::where('status', Loan::STATUS_DEFAULTED)->count();
        $closed = Loan::where('status', Loan::STATUS_CLOSED)->count();
        $stats['default_rate'] = ($defaults + $closed) > 0 ? round(($defaults / ($defaults + $closed)) * 100, 2) : 0;

        $stats['revenue_breakdown'] = [
            'fees' => Transaction::where('type', 'fee')->sum('amount'),
            'penalties' => Transaction::where('type', 'penalty')->sum('amount'),
            'interest' => max($stats['total_repaid'] - Transaction::where('type', 'repayment')->sum('amount'), 0),
        ];

        return $stats;
    }

    public function getLoanDisbursementTrends()
    {
        return Transaction::where('type', 'disbursement')
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getLoanDistributionByProduct()
    {
        return DB::table('loans')
            ->select('loan_product_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('loan_product_id')
            ->get();
    }
}

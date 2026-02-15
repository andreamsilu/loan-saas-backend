<?php

namespace App\Console\Commands;

use App\Modules\Billing\Services\BillingService;
use Illuminate\Console\Command;

class GenerateBillingInvoices extends Command
{
    protected $signature = 'billing:generate-invoices';

    protected $description = 'Generate subscription invoices for due billing periods';

    public function handle(BillingService $billingService): int
    {
        $billingService->generateDueInvoices();
        return self::SUCCESS;
    }
}


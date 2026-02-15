<?php

namespace App\Modules\Billing\Services;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Subscription\Models\Subscription;
use Illuminate\Support\Carbon;

class BillingService
{
    public function generateDueInvoices(): void
    {
        $subs = Subscription::with('plan')
            ->whereIn('status', ['active'])
            ->whereNotNull('current_period_ends_at')
            ->where('current_period_ends_at', '<=', now())
            ->get();

        foreach ($subs as $subscription) {
            $plan = $subscription->plan;
            if (!$plan || $plan->price <= 0) {
                continue;
            }

            $cycle = $subscription->billing_cycle ?: $plan->billing_cycle ?: 'monthly';
            $periodEnd = Carbon::parse($subscription->current_period_ends_at);
            $periodStart = $this->previousPeriodStart($periodEnd, $cycle);

            $existing = Invoice::where('subscription_id', $subscription->id)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->exists();

            if ($existing) {
                $subscription->current_period_ends_at = $this->nextPeriodEnd($periodEnd, $cycle);
                $subscription->save();
                continue;
            }

            $tenant = $subscription->tenant;
            $taxRate = 0;
            if ($tenant && is_array($tenant->settings)) {
                $taxRate = (float)($tenant->settings['tax_rate'] ?? 0);
            }
            $taxAmount = round($plan->price * $taxRate, 2);

            Invoice::create([
                'tenant_id' => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
                'amount' => $plan->price,
                'tax' => $taxAmount,
                'status' => 'unpaid',
                'due_date' => $periodEnd->toDateString(),
                'metadata' => [
                    'billing_cycle' => $cycle,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'tax_rate' => $taxRate,
                ],
            ]);

            $subscription->current_period_ends_at = $this->nextPeriodEnd($periodEnd, $cycle);
            $subscription->save();
        }
    }

    protected function previousPeriodStart(Carbon $end, string $cycle): Carbon
    {
        if ($cycle === 'yearly') {
            return $end->copy()->subYear();
        }
        if ($cycle === 'monthly') {
            return $end->copy()->subMonth();
        }
        return $end->copy()->subMonth();
    }

    protected function nextPeriodEnd(Carbon $currentEnd, string $cycle): Carbon
    {
        if ($cycle === 'yearly') {
            return $currentEnd->copy()->addYear();
        }
        if ($cycle === 'monthly') {
            return $currentEnd->copy()->addMonth();
        }
        return $currentEnd->copy()->addMonth();
    }
}

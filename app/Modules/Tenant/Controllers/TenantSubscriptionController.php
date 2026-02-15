<?php

namespace App\Modules\Tenant\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Subscription\Models\Subscription;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    public function current()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $subscription = Subscription::with('plan')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $plan = $subscription->plan;
        $cycle = $subscription->billing_cycle ?: ($plan ? $plan->billing_cycle : null);

        return response()->json([
            'subscription' => $subscription,
            'plan' => $plan,
            'billing_cycle' => $cycle,
            'next_billing_date' => optional($subscription->current_period_ends_at)->toDateString(),
        ]);
    }

    public function updateBillingCycle(Request $request)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $cycle = $request->input('billing_cycle');
        $now = now();
        $periodEnd = match ($cycle) {
            'yearly' => $now->copy()->addYear(),
            'monthly' => $now->copy()->addMonth(),
            default => $now->copy()->addMonth(),
        };

        $subscription->billing_cycle = $cycle;
        $subscription->current_period_ends_at = $periodEnd;
        $subscription->save();

        return response()->json($subscription);
    }

    public function history()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($invoices);
    }
}

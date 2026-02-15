<?php

namespace App\Modules\Owner\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Audit\Services\AuditService;
use Illuminate\Http\Request;

class OwnerTenantController extends Controller
{
    protected $audit;

    public function __construct(AuditService $audit)
    {
        $this->audit = $audit;
    }

    public function index()
    {
        return response()->json(Tenant::with(['subscription.plan'])->get());
    }

    public function setPlan(Request $request, Tenant $tenant)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'status' => 'sometimes|in:trial,active,suspended,expired,cancelled',
        ]);

        $plan = Plan::findOrFail($request->input('plan_id'));

        $cycle = $plan->billing_cycle;
        $now = now();
        $periodEnd = match ($cycle) {
            'yearly' => $now->copy()->addYear(),
            'monthly' => $now->copy()->addMonth(),
            default => $now->copy()->addMonth(),
        };

        $sub = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $cycle,
            'status' => $request->input('status', 'active'),
            'trial_ends_at' => $now->copy()->addDays(14),
            'current_period_ends_at' => $periodEnd,
        ]);

        $this->audit->record('tenant.subscription.changed', [
            'entity_type' => 'tenant',
            'entity_id' => $tenant->id,
            'new' => ['subscription_id' => $sub->id],
        ]);

        return response()->json($sub, 201);
    }

    public function suspend(Tenant $tenant)
    {
        $sub = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if ($sub) {
            $sub->update(['status' => 'suspended', 'suspended_at' => now()]);
        }
        $this->audit->record('tenant.suspended', [
            'entity_type' => 'tenant',
            'entity_id' => $tenant->id,
        ]);
        return response()->json(['ok' => true]);
    }

    public function activate(Tenant $tenant)
    {
        $sub = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if ($sub) {
            $sub->update(['status' => 'active', 'suspended_at' => null]);
        }
        $this->audit->record('tenant.activated', [
            'entity_type' => 'tenant',
            'entity_id' => $tenant->id,
        ]);
        return response()->json(['ok' => true]);
    }

    public function updateBillingCycle(Request $request, Tenant $tenant)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $sub = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if (!$sub) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $cycle = $request->input('billing_cycle');
        $now = now();
        $periodEnd = match ($cycle) {
            'yearly' => $now->copy()->addYear(),
            'monthly' => $now->copy()->addMonth(),
            default => $now->copy()->addMonth(),
        };

        $sub->billing_cycle = $cycle;
        $sub->current_period_ends_at = $periodEnd;
        $sub->save();

        $this->audit->record('tenant.subscription.billing_cycle_changed', [
            'entity_type' => 'tenant',
            'entity_id' => $tenant->id,
            'new' => ['billing_cycle' => $cycle],
        ]);

        return response()->json($sub);
    }
}

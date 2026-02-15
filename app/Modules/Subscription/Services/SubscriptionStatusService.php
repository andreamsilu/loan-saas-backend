<?php

namespace App\Modules\Subscription\Services;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Subscription\Models\Subscription;
use Illuminate\Support\Carbon;

class SubscriptionStatusService
{
    public function syncStatuses(): void
    {
        $now = Carbon::now();

        Subscription::with('plan')->chunk(100, function ($subs) use ($now) {
            foreach ($subs as $subscription) {
                $updated = false;

                if ($subscription->status === 'trial' && $subscription->trial_ends_at && $now->greaterThan($subscription->trial_ends_at)) {
                    $hasPaid = Invoice::where('subscription_id', $subscription->id)
                        ->where('status', 'paid')
                        ->exists();
                    $subscription->status = $hasPaid ? 'active' : 'suspended';
                    $updated = true;
                }

                if ($subscription->status === 'active' && $subscription->current_period_ends_at) {
                    if ($now->greaterThan($subscription->current_period_ends_at)) {
                        $hasUnpaid = Invoice::where('subscription_id', $subscription->id)
                            ->where('status', 'unpaid')
                            ->where('due_date', '<=', $now->toDateString())
                            ->exists();
                        if ($hasUnpaid) {
                            $subscription->status = 'expired';
                            $updated = true;
                        }
                    }
                }

                if ($updated) {
                    $subscription->save();
                }
            }
        });
    }
}


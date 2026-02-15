<?php

namespace Tests\Feature;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_invoices_and_dashboard_for_monthly_plan()
    {
        $tenant = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);

        $plan = Plan::create([
            'name' => 'Monthly Plan',
            'billing_cycle' => 'monthly',
            'price' => 100,
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'current_period_ends_at' => now()->subDay(),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->artisan('billing:generate-invoices')->assertSuccessful();

        $this->assertDatabaseHas('invoices', [
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'amount' => 100,
            'status' => 'unpaid',
        ]);

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/billing/dashboard')
            ->assertStatus(200)
            ->assertJson([
                'total_amount' => 100,
                'unpaid_amount' => 100,
                'unpaid_count' => 1,
            ]);
    }

    public function test_admin_can_change_billing_cycle_for_subscription()
    {
        $tenant = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);

        $plan = Plan::create([
            'name' => 'Yearly Plan',
            'billing_cycle' => 'yearly',
            'price' => 200,
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => 'yearly',
            'status' => 'active',
            'current_period_ends_at' => now()->subDay(),
        ]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'email' => 'admin@tenant.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tenant/subscription/billing-cycle', [
                'billing_cycle' => 'monthly',
            ])
            ->assertStatus(200)
            ->assertJsonPath('billing_cycle', 'monthly');

        $subscription->refresh();
        $this->assertEquals('monthly', $subscription->billing_cycle);
    }

    public function test_tenant_can_view_current_subscription_info()
    {
        $tenant = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);

        $plan = Plan::create([
            'name' => 'Monthly Plan',
            'billing_cycle' => 'monthly',
            'price' => 150,
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'current_period_ends_at' => now()->addMonth(),
        ]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'email' => 'admin2@tenant.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tenant/subscription/current')
            ->assertStatus(200)
            ->assertJsonPath('subscription.id', $subscription->id)
            ->assertJsonPath('plan.id', $plan->id)
            ->assertJsonPath('billing_cycle', 'monthly')
            ->assertJsonPath('next_billing_date', $subscription->current_period_ends_at->toDateString());
    }

    public function test_tenant_can_view_subscription_history_invoices()
    {
        $tenant = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);

        $plan = Plan::create([
            'name' => 'Monthly Plan',
            'billing_cycle' => 'monthly',
            'price' => 150,
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'current_period_ends_at' => now()->addMonth(),
        ]);

        Invoice::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'amount' => 150,
            'tax' => 0,
            'status' => 'unpaid',
            'due_date' => now()->addMonth()->toDateString(),
            'metadata' => [],
        ]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'email' => 'admin3@tenant.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tenant/subscription/history')
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'amount' => '150.00',
                'status' => 'unpaid',
            ]);
    }
}

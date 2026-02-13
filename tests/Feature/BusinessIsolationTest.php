<?php

namespace Tests\Feature;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use App\Modules\Borrower\Models\Borrower;
use App\Modules\Loan\Models\Loan;
use App\Shared\Services\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_data_is_isolated_by_tenant()
    {
        // 1. Create two tenants
        $tenant1 = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);
        $tenant2 = Tenant::create(['name' => 'Tenant 2', 'subdomain' => 't2']);

        // 2. Create borrower for tenant 1
        $borrower1 = Borrower::create([
            'tenant_id' => $tenant1->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        // 3. Create borrower for tenant 2
        $borrower2 = Borrower::create([
            'tenant_id' => $tenant2->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
        ]);

        $tenantManager = app(TenantManager::class);

        // 4. Verify tenant 1 sees only its borrower
        $tenantManager->setTenant($tenant1);
        $this->assertEquals(1, Borrower::count());
        $this->assertEquals('John', Borrower::first()->first_name);

        // 5. Verify tenant 2 sees only its borrower
        $tenantManager->setTenant($tenant2);
        $this->assertEquals(1, Borrower::count());
        $this->assertEquals('Jane', Borrower::first()->first_name);

        // 6. Test Loan Isolation
        $loan1 = Loan::create([
            'tenant_id' => $tenant1->id,
            'borrower_id' => $borrower1->id,
            'amount' => 1000,
            'interest_rate' => 5.0,
            'duration_months' => 12,
            'status' => 'pending',
        ]);

        $tenantManager->setTenant($tenant1);
        $this->assertEquals(1, Loan::count());

        $tenantManager->setTenant($tenant2);
        $this->assertEquals(0, Loan::count());
    }
}

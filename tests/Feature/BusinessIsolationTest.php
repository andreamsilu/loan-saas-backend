<?php

namespace Tests\Feature;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use App\Modules\Borrower\Models\Borrower;
use App\Modules\Loan\Models\Loan;
use App\Modules\Loan\Models\LoanProduct;
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
            'id_number' => 'ID-T1-001',
            'phone' => '0711111111',
        ]);

        // 3. Create borrower for tenant 2
        $borrower2 = Borrower::create([
            'tenant_id' => $tenant2->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'id_number' => 'ID-T2-001',
            'phone' => '0722222222',
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
        $product1 = LoanProduct::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Test Product',
            'interest_calculation_type' => 'flat',
            'interest_rate' => 5.0,
            'term_duration' => 12,
            'term_period' => 'months',
            'min_amount' => 100,
            'max_amount' => 10000,
            'repayment_frequency' => 'monthly',
        ]);
        $loan1 = Loan::create([
            'tenant_id' => $tenant1->id,
            'loan_product_id' => $product1->id,
            'borrower_id' => $borrower1->id,
            'loan_number' => 'TEST-001',
            'application_date' => now()->toDateString(),
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

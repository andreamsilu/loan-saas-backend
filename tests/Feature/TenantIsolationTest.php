<?php

namespace Tests\Feature;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_works()
    {
        // 1. Create two tenants
        $tenant1 = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);
        $tenant2 = Tenant::create(['name' => 'Tenant 2', 'subdomain' => 't2']);

        // 2. Create users for each tenant
        $user1 = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'tenant_id' => $tenant2->id,
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        // 3. Request with Tenant 1 ID
        $response = $this->withHeaders(['X-Tenant-ID' => $tenant1->id])
                         ->getJson('/api/tenant/me');

        $response->assertStatus(200);
        $response->assertJsonPath('tenant.id', $tenant1->id);

        // 4. Verify user scope (requires setting tenant context manually in test or via middleware)
        // The middleware sets the global context. Let's test if we can see only tenant 1 users.
        
        $this->assertEquals(2, User::withoutGlobalScopes()->count());
        
        // When we set the tenant context, we should only see user1
        app(\App\Shared\Services\TenantManager::class)->setTenant($tenant1);
        $this->assertEquals(1, User::count());
        $this->assertEquals($user1->id, User::first()->id);

        // Switch to tenant 2
        app(\App\Shared\Services\TenantManager::class)->setTenant($tenant2);
        $this->assertEquals(1, User::count());
        $this->assertEquals($user2->id, User::first()->id);
    }
}

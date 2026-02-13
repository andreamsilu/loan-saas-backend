<?php

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant1 = Tenant::create([
            'name' => 'First Tenant',
            'subdomain' => 'tenant1',
            'is_active' => true,
        ]);

        $tenant2 = Tenant::create([
            'name' => 'Second Tenant',
            'subdomain' => 'tenant2',
            'is_active' => true,
        ]);

        User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Tenant 1 Admin',
            'email' => 'admin@tenant1.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);

        User::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Tenant 2 Admin',
            'email' => 'admin@tenant2.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);
    }
}

<?php

namespace Database\Seeders;

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
        $tenant1 = Tenant::firstOrCreate(
            ['subdomain' => 'tenant1'],
            [
                'name' => 'First Tenant',
                'is_active' => true,
            ]
        );

        $tenant2 = Tenant::firstOrCreate(
            ['subdomain' => 'tenant2'],
            [
                'name' => 'Second Tenant',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@tenant1.com'],
            [
                'tenant_id' => $tenant1->id,
                'name' => 'Tenant 1 Admin',
                'password' => bcrypt('password'),
                'role' => UserRole::TENANT_ADMIN,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@tenant2.com'],
            [
                'tenant_id' => $tenant2->id,
                'name' => 'Tenant 2 Admin',
                'password' => bcrypt('password'),
                'role' => UserRole::TENANT_ADMIN,
            ]
        );
    }
}

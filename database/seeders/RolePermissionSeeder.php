<?php

namespace Database\Seeders;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'tenant_admin' => [
                'tenant.manage_settings',
                'user.manage_staff',
                'loan.manage',
                'borrower.manage',
                'report.view',
            ],
            'staff' => [
                'loan.create',
                'loan.view',
                'loan.approve',
                'borrower.create',
                'borrower.view',
                'report.view',
            ],
        ];

        foreach (Tenant::all() as $tenant) {
            foreach ($defaults as $role => $permissions) {
                foreach ($permissions as $permission) {
                    RolePermission::firstOrCreate([
                        'tenant_id' => $tenant->id,
                        'role' => $role,
                        'permission' => $permission,
                    ]);
                }
            }
        }
    }
}

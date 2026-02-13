<?php

namespace Tests\Feature;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_email()
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'subdomain' => 'test']);
        
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::STAFF,
        ]);

        $response = $this->withHeaders(['X-Tenant-ID' => $tenant->id])
                         ->postJson('/api/user/login', [
                             'login' => 'test@example.com',
                             'password' => 'password',
                             'device_name' => 'test_device',
                         ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_user_can_login_with_phone()
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'subdomain' => 'test']);
        
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
            'role' => UserRole::STAFF,
        ]);

        $response = $this->withHeaders(['X-Tenant-ID' => $tenant->id])
                         ->postJson('/api/user/login', [
                             'login' => '1234567890',
                             'password' => 'password',
                             'device_name' => 'test_device',
                         ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_user_can_register()
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'subdomain' => 'test']);

        $response = $this->withHeaders(['X-Tenant-ID' => $tenant->id])
                         ->postJson('/api/user/register', [
                             'name' => 'New User',
                             'email' => 'new@example.com',
                             'phone' => '0987654321',
                             'password' => 'password',
                             'password_confirmation' => 'password',
                             'device_name' => 'test_device',
                         ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['token', 'user']);
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'phone' => '0987654321',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_user_cannot_login_to_wrong_tenant()
    {
        $tenant1 = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);
        $tenant2 = Tenant::create(['name' => 'Tenant 2', 'subdomain' => 't2']);
        
        $user1 = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        // Attempt to login to Tenant 2 with Tenant 1's user credentials
        $response = $this->withHeaders(['X-Tenant-ID' => $tenant2->id])
                         ->postJson('/api/user/login', [
                             'login' => 'user1@example.com',
                             'password' => 'password',
                             'device_name' => 'test_device',
                         ]);

        $response->assertStatus(422);
    }
}

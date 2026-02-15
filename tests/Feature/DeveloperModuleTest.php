<?php

namespace Tests\Feature;

use App\Modules\Developer\Models\ApiKey;
use App\Modules\Developer\Models\ApiUsageLog;
use App\Modules\Developer\Models\WebhookEndpoint;
use App\Modules\Developer\Models\WebhookLog;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeveloperModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_usage_is_logged_for_requests_with_api_key()
    {
        $tenant = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);

        $token = 'test-token-123';
        $key = ApiKey::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Key',
            'token_hash' => hash('sha256', $token),
            'scopes' => [],
            'active' => true,
        ]);

        $this->postJson('/api/user/login', [
            'login' => 'no-user@example.com',
            'password' => 'password',
            'device_name' => 'test',
        ], [
            'X-Tenant-ID' => $tenant->id,
            'X-Api-Key' => $token,
        ]);

        $this->assertDatabaseHas('api_usage_logs', [
            'tenant_id' => $tenant->id,
            'api_key_id' => $key->id,
        ]);
    }

    public function test_webhook_endpoint_can_be_created_and_logs_written()
    {
        $tenant = Tenant::create(['name' => 'Tenant 1', 'subdomain' => 't1']);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'email' => 'admin@tenant.com',
            'password' => bcrypt('password'),
            'role' => UserRole::TENANT_ADMIN,
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        Http::fake([
            'https://example.com/webhook' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/developer/webhooks', [
                'name' => 'Loan Events',
                'url' => 'https://example.com/webhook',
                'events' => ['loan.approved'],
                'active' => true,
                'secret' => 'secret-key',
            ]);

        $response->assertStatus(201);
        $endpointId = $response->json('id');

        $endpoint = WebhookEndpoint::findOrFail($endpointId);

        $service = app(\App\Modules\Developer\Services\WebhookService::class);
        app(\App\Shared\Services\TenantManager::class)->setTenant($tenant);
        $service->dispatch('loan.approved', ['loan_id' => 1]);

        $this->assertDatabaseHas('webhook_logs', [
            'webhook_endpoint_id' => $endpoint->id,
            'event' => 'loan.approved',
            'success' => true,
        ]);
    }
}


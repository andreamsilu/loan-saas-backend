<?php

namespace App\Modules\Developer\Services;

use App\Modules\Developer\Models\WebhookEndpoint;
use App\Modules\Developer\Models\WebhookLog;
use App\Shared\Services\TenantManager;
use Illuminate\Support\Facades\Http;

class WebhookService
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        this->tenantManager = $tenantManager;
    }

    public function dispatch(string $event, array $payload): void
    {
        $tenantId = $this->tenantManager->getTenantId();
        if (!$tenantId) {
            return;
        }

        $endpoints = WebhookEndpoint::where('tenant_id', $tenantId)
            ->where('active', true)
            ->get();

        foreach ($endpoints as $endpoint) {
            $events = $endpoint->events ?? [];
            if (!in_array($event, $events, true)) {
                continue;
            }
            $this->sendToEndpoint($endpoint, $event, $payload);
        }
    }

    protected function sendToEndpoint(WebhookEndpoint $endpoint, string $event, array $payload): void
    {
        $body = [
            'event' => $event,
            'data' => $payload,
        ];

        $headers = [];
        if ($endpoint->secret) {
            $signature = hash_hmac('sha256', json_encode($body), $endpoint->secret);
            $headers['X-Webhook-Signature'] = $signature;
        }

        $status = null;
        $responseBody = null;
        $success = false;

        try {
            $response = Http::timeout(5)->withHeaders($headers)->post($endpoint->url, $body);
            $status = $response->status();
            $responseBody = substr((string) $response->body(), 0, 2000);
            $success = $response->successful();
        } catch (\Throwable $e) {
            $status = null;
            $responseBody = substr($e->getMessage(), 0, 2000);
            $success = false;
        }

        WebhookLog::create([
            'tenant_id' => $endpoint->tenant_id,
            'webhook_endpoint_id' => $endpoint->id,
            'event' => $event,
            'payload' => $payload,
            'response_status' => $status,
            'response_body' => $responseBody,
            'attempts' => 1,
            'last_attempt_at' => now(),
            'success' => $success,
        ]);

        $endpoint->last_used_at = now();
        $endpoint->save();
    }
}


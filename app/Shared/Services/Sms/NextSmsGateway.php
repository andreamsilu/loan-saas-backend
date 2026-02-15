<?php

namespace App\Shared\Services\Sms;

use App\Shared\Interfaces\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class NextSmsGateway implements SmsGatewayInterface
{
    protected TenantSmsConfigService $configService;

    public function __construct(TenantSmsConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function send(string $to, string $message): array
    {
        $config = $this->configService->forCurrentTenant();
        if (!$config) {
            throw new Exception('SMS configuration not found for current tenant.');
        }

        $apiKey = $config['api_key'] ?? null;
        $secretKey = $config['secret_key'] ?? null;
        $from = $config['from'] ?? null;
        $baseUrl = rtrim($config['base_url'] ?? 'https://messaging-service.co.tz', '/');

        if (!$apiKey || !$secretKey || !$from) {
            throw new Exception('NextSMS credentials must be configured by admin.');
        }

        $payload = [
            'from' => $from,
            'to' => $to,
            'text' => $message,
        ];

        $response = Http::baseUrl($baseUrl)
            ->withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':' . $secretKey),
                'Content-Type' => 'application/json',
            ])
            ->post('/api/sms/v1/text/single', $payload);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }
}


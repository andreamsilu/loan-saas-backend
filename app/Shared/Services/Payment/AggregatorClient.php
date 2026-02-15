<?php

namespace App\Shared\Services\Payment;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Shared\Services\Payment\PaymentConfigService;
use App\Shared\Services\TenantManager;
use Exception;

class AggregatorClient
{
    protected PaymentConfigService $configService;
    protected TenantManager $tenantManager;

    public function __construct(PaymentConfigService $configService, TenantManager $tenantManager)
    {
        $this->configService = $configService;
        $this->tenantManager = $tenantManager;
    }

    protected function base(): string
    {
        $cfg = $this->config();
        return rtrim($cfg['base_url'] ?? '', '/');
    }

    protected function config(): array
    {
        $cfg = $this->configService->forCurrentTenant();
        if (!$cfg) {
            throw new Exception('Missing payment configuration');
        }
        return $cfg;
    }

    protected function token(): string
    {
        $tenantId = optional($this->tenantManager->getTenant())->id ?: 'global';
        $cacheKey = 'aggregator_token_'.$tenantId;
        return Cache::remember($cacheKey, now()->addMinutes(25), function () {
            $cfg = $this->config();
            $authPath = $cfg['auth_path'] ?? null;
            $key = $cfg['consumer_key'] ?? null;
            $secret = $cfg['consumer_secret'] ?? null;
            if (!$authPath || !$key || !$secret) {
                throw new Exception('Missing auth configuration');
            }
            $res = Http::acceptJson()->post($this->base().$authPath, [
                'consumer_key' => $key,
                'consumer_secret' => $secret,
            ]);
            $res->throw();
            return $res->json('token') ?? $res->json('access_token');
        });
    }

    public function ensureIpn(): ?string
    {
        $cfg = $this->config();
        $path = $cfg['register_ipn_path'] ?? null;
        $ipnUrl = $cfg['ipn_url'] ?? null;
        if (!$path || !$ipnUrl) {
            return null;
        }
        $tenantId = optional($this->tenantManager->getTenant())->id ?: 'global';
        $cacheKey = 'aggregator_ipn_'.$tenantId;
        return Cache::remember($cacheKey, now()->addDay(), function () use ($cfg, $path, $ipnUrl) {
            $method = $cfg['ipn_method'] ?? 'GET';
            $res = Http::withToken($this->token())
                ->acceptJson()
                ->post($this->base().$path, [
                    'url' => $ipnUrl,
                    'ipn_notification_type' => strtoupper($method),
                ]);
            $res->throw();
            return $res->json('notification_id') ?? $res->json('id');
        });
    }

    public function submitPayment(array $payload): array
    {
        $cfg = $this->config();
        $path = $cfg['submit_order_path'] ?? $cfg['submit_payment_path'] ?? null;
        if (!$path) {
            throw new Exception('Missing payment submission path');
        }
        $res = Http::withToken($this->token())
            ->acceptJson()
            ->post($this->base().$path, $payload);
        $res->throw();
        return $res->json();
    }

    public function payout(array $payload): array
    {
        $cfg = $this->config();
        $path = $cfg['payout_path'] ?? null;
        if (!$path) {
            return ['success' => true, 'reference' => $payload['reference'] ?? ('DSB-'.uniqid())];
        }
        $res = Http::withToken($this->token())
            ->acceptJson()
            ->post($this->base().$path, $payload);
        $res->throw();
        return $res->json();
    }

    public function getStatus(string $trackingId): array
    {
        $cfg = $this->config();
        $path = $cfg['status_path'] ?? null;
        if (!$path) {
            throw new Exception('Missing status path');
        }
        $res = Http::withToken($this->token())
            ->acceptJson()
            ->get($this->base().$path, ['orderTrackingId' => $trackingId]);
        $res->throw();
        return $res->json();
    }
}


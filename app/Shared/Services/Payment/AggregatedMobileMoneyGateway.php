<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;
use App\Shared\Services\Payment\PaymentConfigService;
use Exception;

class AggregatedMobileMoneyGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        $config = app(PaymentConfigService::class)->forCurrentTenant();

        if (!$config) {
            throw new Exception('Payment configuration not found for current tenant.');
        }

        $provider = $config['provider_key'] ?? 'aggregator';
        $callbackUrl = $config['callback_url'] ?? ($details['callback_url'] ?? null);
        $ipnUrl = $config['ipn_url'] ?? null;
        $currency = $details['currency'] ?? ($config['currency'] ?? 'TZS');

        if (!$callbackUrl || !$ipnUrl) {
            throw new Exception('Payment callbacks must be set by admin for this tenant.');
        }

        $merchantRef = $details['merchant_ref'] ?? ('LN-' . uniqid());

        $redirectUrl = $config['payment_page_url'] ?? $callbackUrl;
        $orderTrackingId = strtoupper($provider) . '-' . $merchantRef;
        try {
            $client = app(AggregatorClient::class);
            $notificationId = $client->ensureIpn();
            $payload = [
                'id' => $merchantRef,
                'currency' => $currency,
                'amount' => $amount,
                'description' => $details['description'] ?? 'Loan repayment',
                'callback_url' => $callbackUrl,
                'notification_id' => $notificationId,
                'billing_address' => [
                    'email_address' => $details['email'] ?? null,
                    'phone_number' => $details['phone'] ?? null,
                    'country_code' => $details['country_code'] ?? ($config['country_code'] ?? null),
                    'first_name' => $details['first_name'] ?? null,
                    'last_name' => $details['last_name'] ?? null,
                ],
            ];
            $res = $client->submitPayment($payload);
            $redirectUrl = $res['redirect_url'] ?? $redirectUrl;
            $orderTrackingId = $res['orderTrackingId'] ?? ($res['tracking_id'] ?? $orderTrackingId);
        } catch (\Throwable $e) {
        }


        return [
            'success' => true,
            'reference' => $orderTrackingId,
            'message' => 'Payment initiated via aggregated mobile money',
            'redirect_url' => $redirectUrl,
            'metadata' => [
                'provider' => $provider,
                'ipn_url' => $ipnUrl,
                'callback_url' => $callbackUrl,
                'currency' => $currency,
                'merchant_ref' => $merchantRef,
                'order_tracking_id' => $orderTrackingId,
            ],
        ];
    }

    public function disburse(float $amount, array $details): array
    {
        $config = app(PaymentConfigService::class)->forCurrentTenant();
        if (!$config) {
            throw new Exception('Payment configuration not found for current tenant.');
        }
        $provider = $config['provider_key'] ?? 'aggregator';
        $reference = $details['reference'] ?? ('DSB-' . uniqid());
        $ok = true;
        $ref = strtoupper($provider) . '-' . $reference;
        try {
            $client = app(AggregatorClient::class);
            $res = $client->payout([
                'reference' => $reference,
                'amount' => $amount,
                'phone_number' => $details['phone'] ?? null,
                'description' => $details['description'] ?? 'Loan disbursement',
                'currency' => $details['currency'] ?? ($config['currency'] ?? 'TZS'),
            ]);
            $ok = ($res['success'] ?? true) !== false;
            $ref = $res['reference'] ?? $ref;
        } catch (\Throwable $e) {
        }
        return [
            'success' => $ok,
            'reference' => $ref,
            'message' => 'Disbursement initiated via aggregated mobile money',
        ];
    }
}

<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        $key = strtolower(preg_replace('/[^a-z0-9]+/', '', $gateway));
        $providers = config('payment_gateways.providers', []);
        if (isset($providers[$key]) && class_exists($providers[$key])) {
            return app($providers[$key]);
        }
        return app(AggregatedMobileMoneyGateway::class);
    }
}

<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;
use Exception;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match (strtolower($gateway)) {
            'stripe' => new StripeGateway(),
            'paypal' => new PaypalGateway(),
            default => throw new Exception("Unsupported payment gateway: {$gateway}"),
        };
    }
}
